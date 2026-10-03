<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\AssertableInertia as Assert;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class AuthenticationHardeningTest extends TestCase
{
    #[TestWith([false])]
    #[TestWith([true])]
    public function test_email_login_respects_remember_choice_and_intended_destination(bool $remember): void
    {
        $user = User::factory()->create(['password' => 'LoginPassword2026']);
        $this->withSession(['url.intended' => '/account/settings', 'sentinel' => 'keep']);
        $oldId = session()->getId();

        $response = $this->post('/login', ['email' => strtoupper($user->email), 'password' => 'LoginPassword2026', 'remember' => $remember]);

        $response->assertRedirect('/account/settings')->assertSessionHas('sentinel', 'keep');
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($oldId, session()->getId());
        if ($remember) {
            $response->assertCookie(Auth::getRecallerName());
            $this->assertNotNull($user->fresh()->remember_token);
        } else {
            $response->assertCookieMissing(Auth::getRecallerName());
        }
    }

    public function test_remember_cookie_restores_user_without_existing_session(): void
    {
        $user = User::factory()->create(['password' => 'LoginPassword2026']);
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'LoginPassword2026', 'remember' => true]);
        $cookie = $response->getCookie(Auth::getRecallerName(), false);
        session()->flush();
        Auth::forgetGuards();

        $this->withUnencryptedCookie($cookie->getName(), $cookie->getValue())->get('/account/settings')->assertOk();

        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Auth::viaRemember());
    }

    public function test_logout_revokes_remember_cookie_and_protected_access(): void
    {
        $user = User::factory()->create(['password' => 'LoginPassword2026']);
        $login = $this->post('/login', ['email' => $user->email, 'password' => 'LoginPassword2026', 'remember' => true]);
        $rememberCookie = $login->getCookie(Auth::getRecallerName(), false);
        $oldToken = $user->fresh()->remember_token;
        $oldCsrf = session()->token();

        $this->withUnencryptedCookie($rememberCookie->getName(), $rememberCookie->getValue())->post('/logout')->assertRedirect('/')->assertCookieExpired(Auth::getRecallerName());

        $this->assertGuest();
        $this->assertNotSame($oldToken, $user->fresh()->remember_token);
        $this->assertNotSame($oldCsrf, session()->token());
        $this->get('/account/settings')->assertRedirect(route('login'));
        session()->flush();
        Auth::forgetGuards();
        $this->withUnencryptedCookie($rememberCookie->getName(), $rememberCookie->getValue())->get('/account/settings')->assertRedirect(route('login'));
    }

    #[TestWith(['0812-3456-7890'])]
    #[TestWith(['+62 812-3456-7890'])]
    #[TestWith(['6281234567890'])]
    public function test_phone_password_login_uses_existing_account_for_equivalent_formats(string $phone): void
    {
        $user = User::factory()->create(['phone' => '+62 812-3456-7890', 'password' => 'PhonePassword2026']);

        $this->post('/login', ['phone' => $phone, 'password' => 'PhonePassword2026', 'remember' => true])->assertRedirect('/')->assertCookie(Auth::getRecallerName());

        $this->assertAuthenticatedAs($user);
    }

    public function test_ambiguous_legacy_phone_numbers_never_select_an_account(): void
    {
        User::factory()->create(['phone' => '081234567890', 'password' => 'PhonePassword2026']);
        User::factory()->create(['phone' => '+6281234567890', 'password' => 'PhonePassword2026']);

        $this->post('/login', ['phone' => '081234567890', 'password' => 'PhonePassword2026'])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    #[TestWith(['wrong-password', 'active'])]
    #[TestWith(['PhonePassword2026', 'suspended'])]
    public function test_phone_login_rejects_invalid_password_or_inactive_account(string $password, string $status): void
    {
        User::factory()->create(['phone' => '+6281234567890', 'password' => 'PhonePassword2026', 'status' => $status]);

        $this->post('/login', ['phone' => '081234567890', 'password' => $password])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_google_only_user_cannot_login_with_arbitrary_password(): void
    {
        $user = User::factory()->create(['password' => null, 'google_id' => 'google-only']);

        $this->post('/login', ['email' => $user->email, 'password' => 'Arbitrary2026'])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_phone_registration_normalizes_number_and_retains_email_on_same_user(): void
    {
        $this->post('/register', ['name' => 'Phone traveler', 'email' => 'PHONE@example.test', 'phone' => '0812-3456-7890', 'password' => 'PhonePassword2026', 'password_confirmation' => 'PhonePassword2026', 'role' => 'super_admin'])->assertRedirect('/');

        $user = User::where('email', 'phone@example.test')->firstOrFail();
        $this->assertAuthenticatedAs($user);
        $this->assertSame('+6281234567890', $user->phone);
        $this->assertTrue(Hash::check('PhonePassword2026', $user->password));
        $this->assertFalse($user->hasPermission('admin.access'));
        $this->post('/logout');
        $this->post('/login', ['phone' => '+62 812-3456-7890', 'password' => 'PhonePassword2026'])->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_rejects_phone_already_used_in_another_format(): void
    {
        User::factory()->create(['phone' => '0812-3456-7890']);

        $this->post('/register', ['name' => 'Duplicate', 'email' => 'duplicate@example.test', 'phone' => '+6281234567890', 'password' => 'PhonePassword2026', 'password_confirmation' => 'PhonePassword2026'])->assertSessionHasErrors('phone');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'duplicate@example.test']);
    }

    public function test_profile_update_cannot_claim_another_users_phone(): void
    {
        $owner = User::factory()->create(['phone' => '0812-3456-7890']);
        $user = User::factory()->create(['phone' => null]);

        $this->actingAs($user)->patch('/account/profile', ['name' => $user->name, 'phone' => '+6281234567890'])->assertSessionHasErrors('phone');

        $this->assertNull($user->fresh()->phone);
        $this->assertSame('0812-3456-7890', $owner->fresh()->phone);
    }

    public function test_phone_format_variants_share_login_throttle(): void
    {
        User::factory()->create(['phone' => '+6281234567890', 'password' => 'PhonePassword2026']);
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', ['phone' => '0812-3456-7890', 'password' => 'Wrong2026'])->assertSessionHasErrors('email');
        }

        $this->post('/login', ['phone' => '+62 812-3456-7890', 'password' => 'PhonePassword2026'])->assertTooManyRequests();

        $this->assertGuest();
    }

    #[TestWith([false])]
    #[TestWith([true])]
    public function test_verified_google_identity_links_existing_email_without_forced_persistence(bool $remember): void
    {
        $user = User::factory()->unverified()->create(['password' => 'OriginalPassword2026', 'google_id' => null]);
        $originalPassword = $user->password;
        $this->googleIdentity('linked-google', $user->email);
        $oldId = session()->getId();

        $response = $this->withSession(['socialite_remember' => $remember, 'url.intended' => '/account/settings'])->get('/auth/google/callback');

        $response->assertRedirect('/account/settings')->assertSessionMissing('socialite_remember');
        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($oldId, session()->getId());
        $this->assertSame('linked-google', $user->fresh()->google_id);
        $this->assertSame($originalPassword, $user->fresh()->password);
        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertSame(1, User::where('email', $user->email)->count());
        if ($remember) {
            $response->assertCookie(Auth::getRecallerName());
        } else {
            $response->assertCookieMissing(Auth::getRecallerName());
        }
    }

    public function test_repeated_google_login_keeps_same_user_without_creating_password(): void
    {
        $user = User::factory()->create(['password' => null, 'google_id' => 'repeat-google']);
        $identity = (new \Laravel\Socialite\Two\User)->setRaw(['email_verified' => true])->map(['id' => 'repeat-google', 'name' => 'Google user', 'email' => $user->email]);
        $provider = \Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->twice()->andReturn($identity);
        Socialite::shouldReceive('driver')->with('google')->twice()->andReturn($provider);

        $this->get('/auth/google/callback')->assertRedirect('/');
        $this->post('/logout');
        $this->get('/auth/google/callback')->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->fresh()->password);
        $this->assertSame(1, User::where('google_id', 'repeat-google')->count());
    }

    public function test_oauth_failure_logs_only_exception_type_without_sensitive_payload(): void
    {
        $provider = \Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andThrow(new \RuntimeException('access_token=DO_NOT_LOG'));
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
        Log::shouldReceive('warning')->once()->with('auth.oauth.failed', ['provider' => 'google', 'exception_type' => \RuntimeException::class]);

        $this->get('/auth/google/callback')->assertRedirect('/?auth=login')->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_google_redirect_carries_remember_choice_across_callback_origin(): void
    {
        config(['services.google.client_id' => 'test-client', 'services.google.client_secret' => 'test-secret', 'services.google.redirect' => 'http://localhost:8000/auth/google/callback']);

        $this->get('http://127.0.0.1:8000/auth/google/redirect?remember=1')->assertRedirect('http://localhost:8000/auth/google/redirect?role=traveler&remember=1');
        $this->get('http://localhost:8000/auth/google/redirect?remember=1')->assertSessionHas('socialite_remember', true);
    }

    public function test_sensitive_user_fields_are_not_shared_with_inertia(): void
    {
        $user = User::factory()->create(['google_id' => 'private-google-id', 'remember_token' => 'private-token']);

        $this->actingAs($user)->get('/account/settings')->assertInertia(fn (Assert $page) => $page
            ->where('auth.user.id', $user->id)
            ->missing('auth.user.password')->missing('auth.user.remember_token')->missing('auth.user.google_id'));
    }

    public function test_missing_session_redirects_browser_and_returns_json_unauthenticated(): void
    {
        $this->get('/account/settings')->assertRedirect(route('login'));
        $this->getJson('/account/settings')->assertUnauthorized();
    }

    public function test_password_change_keeps_current_session_and_rejects_stale_session_hash(): void
    {
        $user = User::factory()->create(['password' => 'OriginalPassword2026']);
        $oldHash = $user->password;
        $this->actingAs($user)->withSession(['password_hash_web' => $oldHash]);

        $this->put('/account/password', ['current_password' => 'OriginalPassword2026', 'password' => 'NewPassword2026', 'password_confirmation' => 'NewPassword2026'])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('NewPassword2026', $user->fresh()->password));
        $this->get('/account/settings')->assertOk();
        $this->withSession(['password_hash_web' => $oldHash])->get('/account/settings')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_logout_other_devices_requires_password_and_keeps_current_session(): void
    {
        $user = User::factory()->create(['password' => 'OriginalPassword2026']);
        $otherUser = User::factory()->create(['password' => 'OtherPassword2026']);
        $otherHash = $otherUser->password;
        $oldHash = $user->password;
        $this->actingAs($user)->withSession(['password_hash_web' => $oldHash]);
        $this->post('/account/logout-other-devices', ['current_password' => 'WrongPassword2026'])->assertSessionHasErrors('current_password');
        $this->assertSame($oldHash, $user->fresh()->password);

        $this->post('/account/logout-other-devices', ['current_password' => 'OriginalPassword2026'])->assertSessionHasNoErrors();

        $this->get('/account/settings')->assertOk();
        $this->assertNotSame($oldHash, $user->fresh()->password);
        $this->assertSame($otherHash, $otherUser->fresh()->password);
        $this->withSession(['password_hash_web' => $oldHash])->get('/account/settings')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_password_reset_invalidates_existing_session_and_remember_token(): void
    {
        $user = User::factory()->create(['password' => 'OriginalPassword2026', 'remember_token' => 'old-token']);
        $oldHash = $user->password;
        $token = Password::createToken($user);

        $this->post('/reset-password', ['email' => $user->email, 'token' => $token, 'password' => 'NewPassword2026', 'password_confirmation' => 'NewPassword2026'])->assertRedirect('/?auth=login');

        $this->assertTrue(Hash::check('NewPassword2026', $user->fresh()->password));
        $this->assertNotSame('old-token', $user->fresh()->remember_token);
        $this->actingAs($user->fresh())->withSession(['password_hash_web' => $oldHash])->get('/account/settings')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_authentication_posts_require_csrf_protection(): void
    {
        $user = User::factory()->create(['password' => 'LoginPassword2026']);
        $this->app['env'] = 'local';

        $this->post('/login', ['email' => $user->email, 'password' => 'LoginPassword2026'])->assertStatus(419);

        $this->assertGuest();
        $this->actingAs($user)->post('/logout')->assertStatus(419);
        $this->assertAuthenticatedAs($user);
    }

    public function test_configured_https_session_cookie_has_secure_attributes(): void
    {
        config(['session.secure' => true, 'session.http_only' => true, 'session.same_site' => 'lax']);

        $response = $this->get('/forgot-password');

        $cookie = $response->getCookie(config('session.cookie'), false);
        $this->assertTrue($cookie->isSecure());
        $this->assertTrue($cookie->isHttpOnly());
        $this->assertSame('lax', $cookie->getSameSite());
        $this->assertNull($cookie->getDomain());
    }

    public function test_phone_login_rejects_mixed_identifiers_and_invalid_number(): void
    {
        $user = User::factory()->create(['phone' => '+6281234567890', 'password' => 'PhonePassword2026']);

        $this->post('/login', ['email' => $user->email, 'phone' => '081234567890', 'password' => 'PhonePassword2026'])->assertSessionHasErrors(['email', 'phone']);
        $this->post('/login', ['phone' => 'not-a-phone', 'password' => 'PhonePassword2026'])->assertSessionHasErrors('phone');

        $this->assertGuest();
    }

    public function test_profile_normalizes_own_number_without_rejecting_same_account(): void
    {
        $user = User::factory()->create(['phone' => '0812-3456-7890']);

        $this->actingAs($user)->patch('/account/profile', ['name' => $user->name, 'phone' => '+62 812-3456-7890'])->assertSessionHasNoErrors();

        $this->assertSame('+6281234567890', $user->fresh()->phone);
    }

    public function test_registration_rejects_invalid_phone_without_creating_user(): void
    {
        $this->post('/register', ['name' => 'Invalid phone', 'email' => 'invalid-phone@example.test', 'phone' => 'not-a-phone', 'password' => 'PhonePassword2026', 'password_confirmation' => 'PhonePassword2026'])->assertSessionHasErrors('phone');

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['email' => 'invalid-phone@example.test']);
    }

    public function test_database_rejects_duplicate_canonical_phone_number(): void
    {
        User::factory()->create(['phone' => '+6281234567890']);
        $this->expectException(UniqueConstraintViolationException::class);

        User::factory()->create(['phone' => '+6281234567890']);
    }

    public function test_phone_migration_refuses_equivalent_legacy_numbers_without_changing_accounts(): void
    {
        $first = User::factory()->create(['phone' => '0812-3456-7890']);
        $second = User::factory()->create(['phone' => '+6281234567890']);
        $migration = require database_path('migrations/2026_10_03_100020_add_unique_phone_index_to_users_table.php');

        try {
            $migration->up();
            $this->fail('Migration must reject equivalent phone numbers.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Equivalent user phone numbers exist. Resolve account ownership before applying this migration. No accounts were changed.', $exception->getMessage());
        }

        $this->assertSame('0812-3456-7890', $first->fresh()->phone);
        $this->assertSame('+6281234567890', $second->fresh()->phone);
    }

    public function test_email_login_preserves_legacy_mixed_case_address(): void
    {
        $user = User::factory()->create(['email' => 'Legacy.Address@example.test', 'password' => 'LegacyPassword2026']);

        $this->post('/login', ['email' => 'legacy.address@example.test', 'password' => 'LegacyPassword2026'])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_registration_cannot_duplicate_legacy_email_by_changing_case(): void
    {
        $user = User::factory()->create(['email' => 'Legacy.Address@example.test']);

        $this->post('/register', ['name' => 'Duplicate email', 'email' => 'legacy.address@example.test', 'password' => 'NewPassword2026', 'password_confirmation' => 'NewPassword2026'])->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertModelExists($user);
        $this->assertSame(1, User::whereRaw('lower(email) = ?', ['legacy.address@example.test'])->count());
    }

    public function test_verified_google_email_links_legacy_mixed_case_account(): void
    {
        $user = User::factory()->create(['email' => 'Legacy.Address@example.test', 'google_id' => null]);
        $this->googleIdentity('legacy-google', 'legacy.address@example.test');

        $this->get('/auth/google/callback')->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
        $this->assertSame('legacy-google', $user->fresh()->google_id);
    }

    public function test_ambiguous_legacy_email_cannot_select_an_account_by_password(): void
    {
        User::factory()->create(['email' => 'Ambiguous@example.test', 'password' => 'LegacyPassword2026']);
        User::factory()->create(['email' => 'ambiguous@example.test', 'password' => 'LegacyPassword2026']);

        $this->post('/login', ['email' => 'ambiguous@example.test', 'password' => 'LegacyPassword2026'])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_google_cannot_link_an_ambiguous_legacy_email(): void
    {
        $first = User::factory()->create(['email' => 'Ambiguous@example.test', 'google_id' => null]);
        $second = User::factory()->create(['email' => 'ambiguous@example.test', 'google_id' => null]);
        $this->googleIdentity('ambiguous-google', 'ambiguous@example.test');

        $this->get('/auth/google/callback')->assertSessionHasErrors('email');

        $this->assertGuest();
        $this->assertNull($first->fresh()->google_id);
        $this->assertNull($second->fresh()->google_id);
    }

    private function googleIdentity(string $id, string $email): void
    {
        $identity = (new \Laravel\Socialite\Two\User)->setRaw(['email_verified' => true])->map(['id' => $id, 'name' => 'Google user', 'email' => $email]);
        $provider = \Mockery::mock(GoogleProvider::class);
        $provider->shouldReceive('user')->once()->andReturn($identity);
        Socialite::shouldReceive('driver')->with('google')->once()->andReturn($provider);
    }
}
