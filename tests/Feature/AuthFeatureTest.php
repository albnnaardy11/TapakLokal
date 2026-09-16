<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GoogleProvider;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_google_oauth_redirect_generates_correct_redirect(): void
    {
        $response = $this->get('/auth/google');

        $response->assertStatus(302);
        $this->assertStringContainsString('accounts.google.com', $response->headers->get('Location'));
    }

    public function test_google_callback_creates_and_logs_in_new_user(): void
    {
        $mockSocialiteUser = Mockery::mock(SocialiteUser::class);
        $mockSocialiteUser->shouldReceive('getId')->andReturn('google-test-id-123');
        $mockSocialiteUser->shouldReceive('getName')->andReturn('Ardy Traveler');
        $mockSocialiteUser->shouldReceive('getEmail')->andReturn('ardy.traveler@example.com');
        $mockSocialiteUser->shouldReceive('getAvatar')->andReturn('https://images.unsplash.com/photo-sample');

        $mockProvider = Mockery::mock(GoogleProvider::class);
        $mockProvider->shouldReceive('redirectUrl')->andReturnSelf();
        $mockProvider->shouldReceive('user')->andReturn($mockSocialiteUser);

        Socialite::shouldReceive('driver')->with('google')->andReturn($mockProvider);

        // Delete test user if already exists from prior run
        User::where('email', 'ardy.traveler@example.com')->delete();

        $response = $this->get('/auth/google/callback');

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();

        $user = User::where('email', 'ardy.traveler@example.com')->first();
        $this->assertNotNull($user);
        $this->assertSame('google-test-id-123', $user->google_id);
        $this->assertSame('Ardy Traveler', $user->name);
        $this->assertSame(100, $user->points);

        // Clean up
        $user->delete();
    }

    public function test_dashboard_is_protected_by_auth_middleware(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create([
            'email' => 'testuser.auth@example.com',
            'member_tier' => 'bronze',
            'points' => 100,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);

        $user->delete();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create([
            'email' => 'testlogout.auth@example.com',
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();

        $user->delete();
    }
}
