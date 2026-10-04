<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\CorporateCompany;
use App\Models\CorporateMembership;
use App\Models\CorporateRequest;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PortalSessionIsolationTest extends TestCase
{
    public function test_google_login_on_an_active_traveler_session_returns_to_existing_account(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->withSession(['auth_portal' => 'traveler'])
            ->get('/auth/google/redirect?role=traveler&remember=1')
            ->assertRedirect(route('account'));

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_traveler_cannot_open_owned_corporate_workspace_or_submit_actions(): void
    {
        $user = User::factory()->create();
        $company = CorporateCompany::factory()->create(['owner_id' => $user->id, 'status' => 'verified']);
        CorporateMembership::factory()->create(['user_id' => $user->id, 'corporate_company_id' => $company->id, 'role' => 'owner']);
        $this->actingAs($user)->withSession(['auth_portal' => 'traveler']);
        foreach (['/corporate/login', '/corporate/register', '/corporate/dashboard', '/corporate/companies/'.$company->id] as $path) {
            $this->get($path)->assertForbidden()->assertInertia(fn (Assert $page) => $page->component('Auth/PortalSessionConflict')->where('currentPortal', 'traveler')->where('targetPortal', 'corporate'));
        }
        $this->post('/corporate/register', [])->assertForbidden();
        $this->post('/corporate/companies/'.$company->id.'/members', [])->assertForbidden();
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_and_oauth_cannot_replace_an_active_traveler_session(): void
    {
        $traveler = User::factory()->create();
        $corporate = User::factory()->create(['password' => 'Corporate12345']);
        $this->actingAs($traveler)->withSession(['auth_portal' => 'traveler']);
        $this->post('/corporate/login', ['email' => $corporate->email, 'password' => 'Corporate12345'])->assertForbidden();
        $this->get('/auth/google/redirect?role=corporate')->assertForbidden();
        $this->withSession(['socialite_role' => 'corporate'])->get('/auth/google/callback')->assertForbidden();
        $this->post('/login', ['email' => $corporate->email, 'password' => 'Corporate12345'])->assertForbidden();
        $this->assertAuthenticatedAs($traveler);
    }

    public function test_affiliate_login_shows_the_same_logout_reminder_for_travelers(): void
    {
        $traveler = User::factory()->create();
        $this->actingAs($traveler)->withSession(['auth_portal' => 'traveler']);

        $this->get('/login?portal=affiliate')->assertForbidden()->assertInertia(fn (Assert $page) => $page
            ->component('Auth/PortalSessionConflict')
            ->where('currentPortal', 'traveler')
            ->where('targetPortal', 'affiliate'));

        $this->post('/logout', ['switch_portal' => 'affiliate'])
            ->assertRedirect(route('login', ['portal' => 'affiliate']));
        $this->assertGuest();
        $this->get('/login?portal=affiliate')->assertRedirect('/?auth=login');
    }

    public function test_logout_is_required_before_corporate_login_and_sets_new_portal(): void
    {
        $traveler = User::factory()->create();
        $corporate = User::factory()->create(['password' => 'Corporate12345']);
        $company = CorporateCompany::factory()->create(['owner_id' => $corporate->id]);
        CorporateMembership::factory()->create(['user_id' => $corporate->id, 'corporate_company_id' => $company->id, 'role' => 'owner']);
        $this->actingAs($traveler)->withSession(['auth_portal' => 'traveler']);
        $this->post('/logout', ['switch_portal' => 'corporate'])->assertRedirect(route('corporate.login'))->assertSessionMissing('auth_portal');
        $this->assertGuest();
        $this->post('/corporate/login', ['email' => $corporate->email, 'password' => 'Corporate12345'])->assertRedirect(route('corporate.dashboard'))->assertSessionHas('auth_portal', 'corporate');
        $this->assertAuthenticatedAs($corporate);
        $this->get('/corporate/dashboard')->assertOk();
        $this->get('/account')->assertForbidden();
        $this->get('/admin/login')->assertForbidden();
        $this->get('/vendor/login')->assertForbidden();
    }

    public function test_traveler_session_cannot_use_corporate_booking_checkout_or_legacy_links(): void
    {
        $user = User::factory()->create();
        $booking = Booking::factory()->create(['user_id' => $user->id]);
        CorporateRequest::factory()->create(['user_id' => $user->id, 'booking_id' => $booking->id]);
        $this->actingAs($user)->withSession(['auth_portal' => 'traveler']);
        $this->get('/checkout/trip/'.$booking->id)->assertForbidden();
        $this->post('/checkout/trip/'.$booking->id, ['method' => 'gopay'])->assertForbidden();
        $this->get('/bookings/'.$booking->id)->assertForbidden();
        $this->post('/bookings/'.$booking->id.'/cancel')->assertForbidden();
    }

    public function test_old_unmarked_traveler_session_does_not_gain_corporate_access(): void
    {
        $this->actingAs(User::factory()->create())->get('/corporate/dashboard')->assertForbidden();
    }
}
