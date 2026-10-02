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
        $abstractUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $providerMock = \Mockery::mock(GoogleProvider::class);
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
        $abstractUser->shouldReceive('getRaw')->andReturn(['email_verified' => true]);

        $providerMock = \Mockery::mock(GoogleProvider::class);
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

    private function googleIdentity(string $id, string $email, bool $verified = true): void
    {
        $identity = (new \Laravel\Socialite\Two\User)->setRaw(['email_verified' => $verified])->map(['id' => $id, 'name' => 'Akun pilihan Google', 'email' => $email, 'avatar' => null]);
        $provider = \Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($identity);
        Socialite::shouldReceive('driver')->with('google')->andReturn($provider);
    }

    public function test_google_account_switch_requires_logout_before_selected_account_can_authenticate(): void
    {
        $old = User::factory()->create(['name' => 'Akun lama', 'google_id' => 'old-google']);
        $selected = User::factory()->create(['name' => 'Akun pilihan', 'google_id' => 'selected-google']);
        $this->googleIdentity('selected-google', $selected->email);
        $this->actingAs($old)->withSession(['socialite_role' => 'traveler'])->get('/auth/google/callback')->assertForbidden();
        $this->assertAuthenticatedAs($old);
        $this->post('/logout')->assertRedirect('/');
        $this->withSession(['socialite_role' => 'traveler'])->get('/auth/google/callback')->assertRedirect('/');
        $this->assertAuthenticatedAs($selected);
        $this->assertSame('old-google', $old->fresh()->google_id);
    }

    public function test_google_identity_conflict_does_not_log_in_or_reassign_accounts(): void
    {
        $byId = User::factory()->create(['google_id' => 'google-subject']);
        $byEmail = User::factory()->create(['google_id' => null]);
        $this->googleIdentity('google-subject', $byEmail->email);
        $this->get('/auth/google/callback')->assertRedirect('/?auth=login')->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNull($byEmail->fresh()->google_id);
        $this->assertSame('google-subject', $byId->fresh()->google_id);
    }

    public function test_existing_email_cannot_be_relinked_to_a_different_google_subject(): void
    {
        $user = User::factory()->create(['google_id' => 'original-google']);
        $this->googleIdentity('different-google', $user->email);
        $this->get('/auth/google/callback')->assertRedirect('/?auth=login')->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertSame('original-google', $user->fresh()->google_id);
    }

    public function test_unverified_google_email_cannot_authenticate_existing_user(): void
    {
        $user = User::factory()->create(['google_id' => null]);
        $this->googleIdentity('unverified-google', $user->email, false);
        $this->get('/auth/google/callback')->assertRedirect('/?auth=login')->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertNull($user->fresh()->google_id);
    }

    public function test_google_login_without_configuration_never_simulates_an_account(): void
    {
        config(['services.google.client_id' => null, 'services.google.client_secret' => null]);
        $this->get('/auth/google/redirect')->assertRedirect('/?auth=login')->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_google_login_starts_on_callback_origin_and_keeps_only_relative_return_path(): void
    {
        config(['services.google.client_id' => 'test-client', 'services.google.client_secret' => 'test-secret', 'services.google.redirect' => 'http://localhost:8000/auth/google/callback']);
        $response = $this->withSession(['url.intended' => 'http://127.0.0.1:8000/checkout/review/trip/1'])->get('http://127.0.0.1:8000/auth/google/redirect');
        $response->assertRedirect('http://localhost:8000/auth/google/redirect?role=traveler&intended=%2Fcheckout%2Freview%2Ftrip%2F1');
        $this->get('/auth/google/redirect?intended=https://external.example')->assertSessionHasErrors('intended');
    }

    public function test_google_callback_without_valid_oauth_state_never_authenticates(): void
    {
        $this->get('/auth/google/callback?state=unexpected&code=unused')->assertRedirect('/?auth=login')->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_manual_account_switch_requires_logout_then_checks_new_credentials(): void
    {
        $old = User::factory()->create();
        $selected = User::factory()->create(['password' => 'SelectedPassword2026']);
        $this->actingAs($old)->post('/login', ['email' => $selected->email, 'password' => 'SelectedPassword2026', 'remember' => true])->assertForbidden();
        $this->assertAuthenticatedAs($old);
        $this->post('/logout')->assertRedirect('/');
        $this->post('/login', ['email' => $selected->email, 'password' => 'SelectedPassword2026', 'remember' => true])->assertRedirect('/');
        $this->assertAuthenticatedAs($selected);
    }
}
