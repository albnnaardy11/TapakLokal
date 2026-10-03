<?php

namespace Tests\Feature;

use App\Models\MediaAsset;
use App\Models\User;
use App\Models\VendorApplication;
use App\Services\AccessService;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class VendorApplicationTest extends TestCase
{
    public function test_verified_applicant_can_log_in_with_registration_password(): void
    {
        $this->post(route('vendor-applications.store'), ['track' => 'trip', 'business' => 'Wisata', 'name' => 'Ahmad', 'city' => 'Bogor', 'email' => 'newvendor@gmail.com', 'phone' => '081234567890', 'password' => 'SecurePass123', 'password_confirmation' => 'SecurePass123'])->assertSessionHasNoErrors();
        $user = User::where('email', 'newvendor@gmail.com')->firstOrFail();
        $this->post(route('vendor.login.store'), ['email' => $user->email, 'password' => 'SecurePass123'])->assertSessionHasErrors('email');
        $admin = User::factory()->create();
        app(AccessService::class)->grant($admin, 'operations_admin');
        $user->vendor->update(['document_id' => MediaAsset::factory()->create()->id]);
        $this->actingAs($admin)->post(route('admin.panel.workflow', ['panel' => 'operations', 'module' => 'vendors', 'record' => $user->vendor->id, 'action' => 'verify']))->assertSessionHasNoErrors();
        $this->post(route('logout'));
        $this->post(route('vendor.login.store'), ['email' => $user->email, 'password' => 'SecurePass123'])->assertSessionHasNoErrors()->assertRedirect(route('vendor.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_password_confirmation_is_required(): void
    {
        $this->post(route('vendor-applications.store'), ['password' => 'SecurePass123', 'password_confirmation' => 'Different123'])->assertSessionHasErrors('password');
        $this->assertDatabaseCount('vendor_applications', 0);
    }

    public function test_non_gmail_address_and_invalid_phone_are_rejected(): void
    {
        $this->post(route('vendor-applications.store'), ['track' => 'trip', 'business' => 'Wisata', 'name' => 'Ahmad', 'city' => 'Bogor', 'email' => 'ahmad@gmail.com.example.org', 'phone' => 'abcdefgh'])->assertSessionHasErrors(['email', 'phone']);
        $this->assertDatabaseCount('vendor_applications', 0);
    }

    public function test_guest_application_is_saved_and_only_vendor_admins_are_notified(): void
    {
        $operations = User::factory()->create();
        $content = User::factory()->create();
        app(AccessService::class)->grant($operations, 'operations_admin');
        app(AccessService::class)->grant($content, 'content_admin');
        $this->post(route('vendor-applications.store'), ['track' => 'trip', 'business' => 'Jelajah Bogor', 'name' => 'Ahmad', 'city' => 'Bogor', 'email' => 'ahmad@gmail.com', 'phone' => '081234567890', 'password' => 'SecurePass123', 'password_confirmation' => 'SecurePass123', 'notes' => 'Open trip Jawa Barat', 'status' => 'approved'])->assertRedirect()->assertSessionHas('success');
        $application = VendorApplication::firstOrFail();
        $this->assertSame('pending', $application->status);
        $applicant = User::findOrFail($application->user_id);
        $this->assertTrue(Hash::check('SecurePass123', $applicant->password));
        $this->assertFalse($applicant->hasPermission('vendor.access'));
        $this->assertSame('pending', $applicant->vendor->status);
        $this->assertSame(1, $operations->notifications()->where('type', 'vendor.application')->count());
        $this->assertSame(0, $content->notifications()->count());
        $url = route('admin.panel.resources.show', ['panel' => 'operations', 'module' => 'vendor-applications', 'record' => $application->id]);
        $this->actingAs($operations)->get($url)->assertOk()->assertInertia(fn (Assert $page) => $page->where('record.business', 'Jelajah Bogor')->where('record.email', 'ahmad@gmail.com')->where('record.phone', '081234567890'));
        $this->put(route('admin.panel.resources.update', ['panel' => 'operations', 'module' => 'vendor-applications', 'record' => $application->id]), ['status' => 'reviewing', 'review_note' => 'Hubungi penanggung jawab'])->assertRedirect();
        $this->assertSame('reviewing', $application->fresh()->status);
        $this->actingAs($content)->get($url)->assertForbidden();
    }

    public function test_invalid_contact_is_rejected_without_saving(): void
    {
        $this->post(route('vendor-applications.store'), ['track' => 'other', 'contact' => 'invalid'])->assertSessionHasErrors(['track', 'business', 'name', 'city', 'email', 'phone']);
        $this->assertDatabaseCount('vendor_applications', 0);
    }
}
