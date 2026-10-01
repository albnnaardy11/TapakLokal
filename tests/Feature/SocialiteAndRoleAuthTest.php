<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\AccessService;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Tests\TestCase;

class SocialiteAndRoleAuthTest extends TestCase
{
    public function test_socialite_redirect_points_to_google_oauth_with_account_prompt(): void
    {
        $response = $this->get('/auth/google/redirect?role=traveler');
        $location = $response->headers->get('Location') ?? '';

        $this->assertStringContainsString('accounts.google.com', $location);
        $this->assertStringContainsString('prompt=select_account', $location);
    }

    public function test_socialite_callback_authenticates_traveler(): void
    {
        $abstractUser = \Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getId')->andReturn('google_test_123');
        $abstractUser->shouldReceive('getName')->andReturn('Traveler Google');
        $abstractUser->shouldReceive('getEmail')->andReturn('traveler.google@tapaklokal.test');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://images.unsplash.com/avatar');
        $abstractUser->shouldReceive('getNickname')->andReturn(null);

        $providerMock = \Mockery::mock(GoogleProvider::class);
        $providerMock->shouldReceive('stateless')->andReturnSelf();
        $providerMock->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($providerMock);

        $response = $this->withSession(['socialite_role' => 'traveler'])
            ->get('/auth/google/callback');

        $response->assertRedirect('/');

        $user = User::where('email', 'traveler.google@tapaklokal.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(['traveler'], $user->roles->pluck('name')->all());
        $this->assertSame('google_test_123', $user->google_id);
    }

    public function test_socialite_callback_authenticates_vendor(): void
    {
        $abstractUser = \Mockery::mock(\Laravel\Socialite\Two\User::class);
        $abstractUser->shouldReceive('getId')->andReturn('google_vendor_123');
        $abstractUser->shouldReceive('getName')->andReturn('Mitra Wisata');
        $abstractUser->shouldReceive('getEmail')->andReturn('mitra.google@tapaklokal.test');
        $abstractUser->shouldReceive('getAvatar')->andReturn('https://images.unsplash.com/avatar');
        $abstractUser->shouldReceive('getNickname')->andReturn(null);

        $providerMock = \Mockery::mock(GoogleProvider::class);
        $providerMock->shouldReceive('stateless')->andReturnSelf();
        $providerMock->shouldReceive('user')->andReturn($abstractUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($providerMock);

        $response = $this->withSession(['socialite_role' => 'vendor'])
            ->get('/auth/google/callback');

        $response->assertRedirect('/vendor');

        $user = User::where('email', 'mitra.google@tapaklokal.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->hasPermission('vendor.access'));
        $this->assertDatabaseHas('vendors', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);
    }

    public function test_manual_vendor_registration_creates_vendor_profile_and_role(): void
    {
        $response = $this->post('/register', [
            'name' => 'Mitra Bromo Tour',
            'email' => 'bromo.partner@example.test',
            'password' => 'BromoPartner2026',
            'password_confirmation' => 'BromoPartner2026',
            'role' => 'vendor',
        ]);

        $response->assertRedirect('/vendor');

        $user = User::where('email', 'bromo.partner@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(['vendor_admin'], $user->roles->pluck('name')->all());
        $this->assertDatabaseHas('vendors', [
            'user_id' => $user->id,
            'name' => 'Mitra Bromo Tour',
            'email' => 'bromo.partner@example.test',
            'status' => 'pending',
        ]);
    }

    public function test_registered_users_appear_in_admin_panel(): void
    {
        $admin = User::factory()->create();
        app(AccessService::class)->grant($admin, 'super_admin');

        $traveler = User::factory()->create(['name' => 'Traveler Test User', 'email' => 'traveler.query@example.test']);
        app(AccessService::class)->grant($traveler, 'traveler');

        $this->actingAs($admin)
            ->get('/admin/users?q=traveler.query')
            ->assertOk();
    }
}
