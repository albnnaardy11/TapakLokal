<?php

namespace Tests\Feature;

use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class RouteLoadingTest extends TestCase
{
    /**
     * Test the welcome / home route renders the Welcome Inertia component.
     */
    public function test_welcome_page_renders_successfully(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Welcome')
            ->has('appName')
        );
    }

    /**
     * Test the blog index route renders the Blog Inertia component.
     */
    public function test_blog_index_page_renders_successfully(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Blog')
        );
    }

    /**
     * Test the blog detail route renders the BlogDetail Inertia component with articleId.
     */
    public function test_blog_detail_page_renders_successfully(): void
    {
        $response = $this->get('/blog/bali');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('BlogDetail')
            ->where('articleId', 'bali')
        );
    }

    /**
     * Test the accessibility guide route renders the AccessibilityGuide Inertia component.
     */
    public function test_accessibility_guide_page_renders_successfully(): void
    {
        $response = $this->get('/panduan-aksesibilitas');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('AccessibilityGuide')
        );
    }

    /**
     * Test the account page renders the Account Inertia component.
     */
    public function test_account_page_renders_successfully(): void
    {
        $response = $this->get('/account');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Account')
        );
    }

    /**
     * Test the open trip detail route renders the TripDetail Inertia component.
     */
    public function test_open_trip_detail_page_renders_successfully(): void
    {
        $response = $this->get('/trips/open-trip/pulau-pramuka');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('TripDetail')
            ->where('tripType', 'open-trip')
            ->where('trip', 'pulau-pramuka')
        );
    }

    /**
     * Test the private trip detail route renders the TripDetail Inertia component.
     */
    public function test_private_trip_detail_page_renders_successfully(): void
    {
        $response = $this->get('/trips/private-trip/labuan-bajo');

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('TripDetail')
            ->where('tripType', 'private-trip')
            ->where('trip', 'labuan-bajo')
        );
    }

    /**
     * Test invalid trip type aborts with 404.
     */
    public function test_invalid_trip_type_returns_404(): void
    {
        $response = $this->get('/trips/unknown-type/something');

        $response->assertStatus(404);
    }
}
