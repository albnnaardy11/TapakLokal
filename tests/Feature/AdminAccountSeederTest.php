<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AccessService;
use Database\Seeders\AdminAccountSeeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminAccountSeederTest extends TestCase
{
    public function test_seeder_creates_five_separate_admin_accounts_and_never_resets_existing_accounts(): void
    {
        Storage::fake('local');
        $this->seed(AdminAccountSeeder::class);
        $this->assertDatabaseCount('users', 5);
        $passwords = User::orderBy('id')->pluck('password', 'email')->all();
        $this->seed(AdminAccountSeeder::class);
        $this->assertSame($passwords, User::orderBy('id')->pluck('password', 'email')->all());
        $files = Storage::disk('local')->files('bootstrap');
        $this->assertCount(1, $files);
        Storage::disk('local')->assertExists($files[0]);
        $credentials = json_decode(Storage::disk('local')->get($files[0]), true);
        foreach ($credentials as $account) {
            $user = User::where('email', $account['email'])->firstOrFail();
            $this->assertTrue(Hash::check($account['password'], $user->password));
            $this->assertSame([$account['role']], $user->roles->pluck('name')->all());
            $this->assertTrue($user->must_change_password);
        }
    }

    public function test_seeded_admin_must_change_password_before_opening_panel(): void
    {
        $user = User::factory()->create(['must_change_password' => true]);
        app(AccessService::class)->grant($user, 'content_admin');
        $this->actingAs($user)->get('/admin/content')->assertRedirect('/password/change');
        $this->put('/account/password', ['current_password' => 'password', 'password' => 'PersonalPassword123!', 'password_confirmation' => 'PersonalPassword123!'])->assertRedirect('/admin');
        $this->assertFalse($user->fresh()->must_change_password);
        $this->get('/admin/content')->assertOk();
    }

    public function test_existing_traveler_email_is_not_silently_promoted_to_admin(): void
    {
        $user = User::factory()->create(['email' => config('admin_accounts.super_admin.email')]);
        try {
            $this->seed(AdminAccountSeeder::class);
            $this->fail('Seeder must refuse an existing unrelated account.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('role berbeda', $exception->getMessage());
        }
        $this->assertSame(0, $user->roles()->count());
        $this->assertDatabaseCount('users', 1);
    }

    public function test_production_rejects_default_demo_addresses_before_creating_accounts(): void
    {
        $this->app->detectEnvironment(fn () => 'production');
        try {
            $this->artisan('db:seed', ['--class' => AdminAccountSeeder::class, '--force' => true])->run();
            $this->fail('Production must require explicit credentials.');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('produksi', $exception->getMessage());
        } finally {
            $this->app->detectEnvironment(fn () => 'testing');
        }
        $this->assertDatabaseCount('users', 0);
    }
}
