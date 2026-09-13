<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageRoutesTest extends TestCase
{
    /**
     * Test that all main pages return successful 200 responses.
     */
    public function test_all_main_pages_return_successful_response(): void
    {
        $routes = [
            '/',
            '/open-trip',
            '/open-po',
            '/tentang-kami',
            '/wishlist',
            '/blog',
            '/member-tiers',
            '/gabung-mitra',
            '/trip/1',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }

    /**
     * Test that an invalid trip returns 404.
     */
    public function test_invalid_trip_returns_404(): void
    {
        $response = $this->get('/trip/999');
        $response->assertStatus(404);
    }
}
