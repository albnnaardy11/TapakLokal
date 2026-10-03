<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class CanonicalLocalHostTest extends TestCase
{
    public function test_local_ip_redirects_to_configured_host_with_path_and_query(): void
    {
        $this->app['env'] = 'local';
        config(['app.url' => 'http://localhost:8000']);

        $this->get('http://127.0.0.1:8000/account?section=settings')->assertRedirect('http://localhost:8000/account?section=settings');
    }

    public function test_reopening_local_ip_returns_to_existing_authenticated_account(): void
    {
        $user = User::factory()->create();
        $this->app['env'] = 'local';
        config(['app.url' => 'http://localhost:8000']);

        $this->actingAs($user)->get('http://127.0.0.1:8000/')->assertRedirect('http://localhost:8000/');
        $this->get('http://localhost:8000/account')->assertOk();

        $this->assertAuthenticatedAs($user);
    }

    public function test_canonical_host_does_not_redirect_again(): void
    {
        $this->app['env'] = 'local';
        config(['app.url' => 'http://localhost:8000']);

        $this->get('http://localhost:8000/forgot-password')->assertOk();
    }

    public function test_production_does_not_apply_local_host_redirect(): void
    {
        $this->app['env'] = 'production';
        config(['app.url' => 'http://localhost:8000']);

        $this->get('http://127.0.0.1:8000/forgot-password')->assertOk();
    }

    public function test_oauth_query_is_preserved_when_canonicalizing_local_host(): void
    {
        $this->app['env'] = 'local';
        config(['app.url' => 'http://localhost:8000']);

        $this->get('http://127.0.0.1:8000/auth/google/redirect?role=traveler&remember=1')->assertRedirect('http://localhost:8000/auth/google/redirect?role=traveler&remember=1');
    }
}
