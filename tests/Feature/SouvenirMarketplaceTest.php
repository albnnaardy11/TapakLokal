<?php

namespace Tests\Feature;

use App\Models\SouvenirProduct;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SouvenirMarketplaceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['platform.markup_bps' => 1000]);
    }

    public function test_catalog_reads_only_published_products_of_verified_vendors(): void
    {
        $product = SouvenirProduct::factory()->create();
        SouvenirProduct::factory()->create(['status' => 'draft']);
        $hidden = SouvenirProduct::factory()->create();
        $hidden->vendor->update(['status' => 'suspended']);

        $this->get('/oleh-oleh?q=kopi')->assertInertia(fn (Assert $page) => $page->component('SouvenirMarketplace')->has('products', 1)->where('products.0.id', $product->slug)->where('products.0.price', 55000));
        $this->get('/oleh-oleh/produk/'.$hidden->slug)->assertNotFound();
    }

    public function test_product_and_store_pages_resolve_database_records(): void
    {
        $product = SouvenirProduct::factory()->create();

        $this->get('/oleh-oleh/produk/'.$product->slug)->assertInertia(fn (Assert $page) => $page->where('view', 'product')->where('productId', $product->slug));
        $this->get('/oleh-oleh/toko/'.$product->vendor_id)->assertInertia(fn (Assert $page) => $page->where('view', 'store')->has('products', 1));
        $this->get('/oleh-oleh/produk/tidak-ada')->assertNotFound();
        $this->get('/oleh-oleh/keranjang')->assertRedirect(route('login'));
        $this->actingAs(User::factory()->create())->get('/oleh-oleh/keranjang')->assertInertia(fn (Assert $page) => $page->where('view', 'cart')->has('cartItems', 0));
    }

    public function test_catalog_paginates_and_filters_prices_on_the_server(): void
    {
        SouvenirProduct::factory()->count(25)->create();
        $this->get('/oleh-oleh')->assertInertia(fn (Assert $page) => $page->has('products', 24)->where('pagination.next', fn ($url) => str_contains($url, 'cursor=')));
        $this->get('/oleh-oleh?min=60000')->assertInertia(fn (Assert $page) => $page->has('products', 0));
    }

    public function test_cursor_pagination_keeps_equal_price_products_distinct(): void
    {
        $products = SouvenirProduct::factory()->count(25)->create();
        $first = $this->get('/oleh-oleh?sort=cheapest');
        $first->assertInertia(fn (Assert $page) => $page->has('products', 24));
        $nextUrl = $first->viewData('page')['props']['pagination']['next'];

        $this->get($nextUrl)->assertInertia(fn (Assert $page) => $page->has('products', 1)->where('products.0.databaseId', $products->first()->id));
    }
}
