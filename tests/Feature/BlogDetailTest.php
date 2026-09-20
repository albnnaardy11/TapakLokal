<?php

namespace Tests\Feature;

use Tests\TestCase;

class BlogDetailTest extends TestCase
{
    /**
     * Test the blog index page returns 200.
     */
    public function test_blog_index_page_returns_successful_response(): void
    {
        $response = $this->get('/blog');

        $response->assertStatus(200);
    }

    /**
     * Test the blog detail page returns 200 with article prop.
     */
    public function test_blog_detail_page_returns_successful_response(): void
    {
        $response = $this->get('/blog/bali');

        $response->assertStatus(200);
    }

    /**
     * Test other article detail pages work correctly.
     */
    public function test_other_blog_articles_return_successful_response(): void
    {
        $response = $this->get('/blog/islands');
        $response->assertStatus(200);

        $response = $this->get('/blog/local');
        $response->assertStatus(200);
    }
}
