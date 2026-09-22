<?php

namespace Tests\Feature;

use App\Models\{MediaAsset, Role, User, Vendor};
use App\Services\AccessService;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PlatformAccessTest extends TestCase
{
    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/account')->assertRedirect('/login');
        $this->get('/admin')->assertRedirect('/login');
        $this->get('/vendor')->assertRedirect('/login');
    }

    public static function adminPermissions(): array
    {
        return [
            'content' => ['content_admin', 'content.manage', 'refund.approve'],
            'operations' => ['operations_admin', 'vendor.verify', 'roles.manage'],
            'finance' => ['finance_admin', 'refund.approve', 'content.publish'],
            'growth' => ['growth_admin', 'growth.manage', 'operations.manage'],
            'super' => ['super_admin', 'roles.manage', 'nonexistent.permission'],
        ];
    }

    #[DataProvider('adminPermissions')]
    public function test_admin_roles_enforce_permission_boundaries(string $role, string $allowed, string $denied): void
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, $role);
        $this->assertTrue($user->can($allowed));
        $this->assertFalse($user->can($denied));
        $this->actingAs($user)->get('/admin')->assertInertia(fn (Assert $page) => $page->component('Admin/Dashboard'));
    }

    public function test_traveler_cannot_enter_admin_or_vendor_panels(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/admin')->assertForbidden();
        $this->get('/vendor')->assertForbidden();
        $this->post('/admin/modules/blog', [])->assertForbidden();
    }

    public function test_registration_cannot_assign_an_admin_role(): void
    {
        $this->post('/register', ['name' => 'Traveler', 'email' => 'traveler@example.test', 'password' => 'SecurePass123', 'password_confirmation' => 'SecurePass123', 'role' => 'super_admin', 'status' => 'suspended'])->assertRedirect('/account');
        $user = User::where('email', 'traveler@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame(['traveler'], $user->roles->pluck('name')->all());
        $this->get('/account')->assertOk();
    }

    public function test_suspended_user_is_logged_out(): void
    {
        $user = User::factory()->create(['status' => 'suspended']);
        $this->actingAs($user)->get('/account')->assertForbidden();
        $this->assertGuest();
    }

    public function test_reseeding_does_not_restore_revoked_permissions(): void
    {
        $role = Role::where('name', 'content_admin')->firstOrFail();
        $role->permissions()->detach();
        app(AccessService::class)->seed();
        $this->assertSame(0, $role->permissions()->count());
    }

    public function test_content_admin_cannot_download_another_users_private_file(): void
    {
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, 'content_admin');
        $media = MediaAsset::factory()->create(['visibility' => 'private']);
        $this->actingAs($user)->get('/media/'.$media->id)->assertNotFound();
        $this->get('/admin/modules/media/'.$media->id)->assertNotFound();
    }

    public function test_verifier_can_only_download_documents_submitted_for_verification(): void
    {
        Storage::fake('local');
        $user = User::factory()->create();
        app(AccessService::class)->grant($user, 'operations_admin');
        $media = MediaAsset::factory()->create(['visibility' => 'private', 'disk' => 'local', 'path' => 'media/document.pdf']);
        Storage::disk('local')->put($media->path, 'document');
        $this->actingAs($user)->get('/media/'.$media->id)->assertNotFound();
        Vendor::factory()->create(['document_id' => $media->id, 'user_id' => $media->user_id]);
        $this->get('/media/'.$media->id)->assertDownload($media->name);
        Storage::disk('local')->assertExists($media->path);
    }
}
