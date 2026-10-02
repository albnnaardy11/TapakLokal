<?php

namespace Tests\Feature;

use App\Models\SouvenirProduct;
use App\Models\Trip;
use App\Models\User;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    public function test_sitemaps_include_published_products_and_exclude_private_or_draft_pages(): void
    {
        $published = SouvenirProduct::factory()->create();
        $draft = SouvenirProduct::factory()->create(['status' => 'draft']);

        $this->assertStringContainsString('/sitemap/products/1.xml', $this->get('/sitemap.xml')->assertOk()->streamedContent());
        $xml = $this->get('/sitemap/products/1.xml')->assertOk()->streamedContent();
        $this->assertStringContainsString($published->slug, $xml);
        $this->assertStringNotContainsString($draft->slug, $xml);
        $this->assertStringNotContainsString('/account', $this->get('/sitemap/static/1.xml')->assertOk()->streamedContent());
        $this->get('/robots.txt')->assertOk()->assertSee('Disallow: /oleh-oleh/pesanan', false);
    }

    public function test_trip_sitemap_builds_the_correct_trip_url(): void
    {
        $trip = Trip::factory()->create(['type' => 'open-trip']);

        $this->assertStringContainsString(route('trips.show', ['tripType' => 'open-trip', 'trip' => $trip->slug]), $this->get('/sitemap/trips/1.xml')->assertOk()->streamedContent());
        $this->get('/sitemap/products/0.xml')->assertNotFound();
    }

    public function test_initial_html_contains_product_metadata_and_private_pages_are_noindex(): void
    {
        $product = SouvenirProduct::factory()->create();

        $this->get('/oleh-oleh/produk/'.$product->slug)->assertSee('<meta name="description"', false)->assertSee(route('souvenirs.show', $product->slug), false);
        $this->actingAs(User::factory()->create())->get('/oleh-oleh/keranjang')->assertSee('noindex,nofollow', false);
    }
}
