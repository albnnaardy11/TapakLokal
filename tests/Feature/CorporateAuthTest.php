<?php

namespace Tests\Feature;

use App\Models\CorporateCompany;
use App\Models\CorporateMembership;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Tests\TestCase;

class CorporateAuthTest extends TestCase
{
    public function test_corporate_guests_use_dedicated_form_and_preserve_registration_destination(): void
    {
        $this->get('/corporate/dashboard')->assertRedirect(route('corporate.login'));
        $this->get('/corporate/login')->assertInertia(fn (Assert $page) => $page->component('Auth/CorporateLogin')->where('destination', 'dashboard'));
        $this->get('/corporate/start')->assertRedirect(route('corporate.login'));
        $this->get('/corporate/login')->assertInertia(fn (Assert $page) => $page->component('Auth/CorporateLogin')->where('destination', 'register'));
    }

    public function test_finance_login_goes_to_corporate_and_only_shows_own_memberships(): void
    {
        $user = User::factory()->create(['password' => 'Corporate12345']);
        $company = CorporateCompany::factory()->create();
        CorporateMembership::factory()->create(['user_id' => $user->id, 'corporate_company_id' => $company->id, 'role' => 'finance']);
        CorporateMembership::factory()->create();
        $this->withSession(['url.intended' => '/admin'])->post('/corporate/login', ['email' => $user->email, 'password' => 'Corporate12345', 'next' => 'dashboard'])->assertRedirect(route('corporate.dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->get('/corporate/dashboard')->assertOk();
        $this->get('/corporate/companies/'.$company->id)->assertInertia(fn (Assert $page) => $page->where('memberRole', 'finance')->where('company.id', $company->id));
    }

    public function test_invalid_credentials_stay_on_corporate_and_do_not_authenticate(): void
    {
        $user = User::factory()->create(['password' => 'Corporate12345']);
        $this->from('/corporate/login')->post('/corporate/login', ['email' => $user->email, 'password' => 'wrong'])->assertRedirect('/corporate/login')->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_pic_registration_requires_consent_and_never_grants_submitted_role(): void
    {
        $data = ['name' => 'PIC Test', 'email' => 'pic.test@example.test', 'password' => 'Corporate12345', 'password_confirmation' => 'Corporate12345', 'role' => 'super_admin', 'next' => 'register'];
        $this->post('/corporate/account', $data)->assertSessionHasErrors('consent');
        $this->post('/corporate/account', [...$data, 'consent' => true])->assertRedirect(route('corporate.register'));
        $user = User::where('email', $data['email'])->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertFalse($user->hasPermission('admin.access'));
        $this->assertFalse($user->hasPermission('vendor.access'));
        $this->assertDatabaseMissing('corporate_memberships', ['user_id' => $user->id]);
    }

    public function test_google_failure_returns_to_corporate_form(): void
    {
        $provider = \Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andThrow(new \RuntimeException('Invalid state'));
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
        $this->withSession(['socialite_role' => 'corporate'])->get('/auth/google/callback')->assertRedirect(route('corporate.login'))->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_google_corporate_login_returns_to_workspace_without_traveler_flash(): void
    {
        $user = User::factory()->create(['google_id' => 'corporate-google-id']);
        $social = new \Laravel\Socialite\Two\User;
        $social->setRaw(['email_verified' => true])->map(['id' => 'corporate-google-id', 'name' => $user->name, 'email' => $user->email]);
        $provider = \Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->andReturn($social);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
        $this->withSession(['socialite_role' => 'corporate', 'url.intended' => route('corporate.dashboard')])->get('/auth/google/callback')->assertRedirect(route('corporate.dashboard'))->assertSessionMissing('login_success_data');
        $this->assertAuthenticatedAs($user);
    }

    public function test_traveler_without_corporate_membership_cannot_login_to_corporate_dashboard(): void
    {
        $user = User::factory()->create(['password' => 'Wisatawan12345']);

        $this->from('/corporate/login')
            ->post('/corporate/login', [
                'email' => $user->email,
                'password' => 'Wisatawan12345',
                'next' => 'dashboard',
            ])
            ->assertRedirect('/corporate/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_traveler_with_next_register_can_login_and_is_redirected_to_company_registration(): void
    {
        $user = User::factory()->create(['password' => 'Wisatawan12345']);

        $this->post('/corporate/login', [
            'email' => $user->email,
            'password' => 'Wisatawan12345',
            'next' => 'register',
        ])->assertRedirect(route('corporate.register'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_traveler_without_membership_is_redirected_from_corporate_dashboard_to_register(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->withSession(['auth_portal' => 'corporate'])
            ->get('/corporate/dashboard')
            ->assertRedirect(route('corporate.register'));
    }
}
