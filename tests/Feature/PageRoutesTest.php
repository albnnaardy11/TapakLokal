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
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
        }
    }
}
