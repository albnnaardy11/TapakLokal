<?php

namespace Tests\Feature;

use App\Models\SouvenirOrder;
use App\Models\SouvenirOrderItem;
use App\Models\SouvenirReview;
use App\Models\User;
use App\Services\AccessService;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SouvenirReviewTest extends TestCase
{
    public function test_only_owner_of_completed_order_can_review_and_duplicates_are_rejected(): void
    {
        $item = SouvenirOrderItem::factory()->create();
        $order = SouvenirOrder::findOrFail($item->souvenir_order_id);
        $payload = ['item_id' => $item->id, 'rating' => 5, 'body' => 'Produk sesuai pesanan, kemasan rapi.'];

        $this->actingAs(User::factory()->create())->post('/oleh-oleh/ulasan', $payload)->assertNotFound();
        $order->update(['status' => 'processing']);
        $this->actingAs(User::findOrFail($order->user_id))->post('/oleh-oleh/ulasan', $payload)->assertNotFound();
        $order->update(['status' => 'completed']);
        $this->post('/oleh-oleh/ulasan', $payload)->assertRedirect();
        $this->post('/oleh-oleh/ulasan', $payload)->assertSessionHasErrors('review');

        $this->assertDatabaseCount('souvenir_reviews', 1);
        $this->assertDatabaseHas('souvenir_reviews', ['souvenir_order_item_id' => $item->id, 'rating' => 5, 'status' => 'pending']);
    }

    public function test_reviews_only_appear_publicly_after_operations_moderation(): void
    {
        $review = SouvenirReview::factory()->create();
        $this->get('/oleh-oleh/produk/'.$review->product->slug)->assertInertia(fn (Assert $page) => $page->has('reviews', 0));
        $admin = User::factory()->create();
        app(AccessService::class)->grant($admin, 'operations_admin');

        $this->actingAs($admin)->put('/admin/ulasan-oleh-oleh/'.$review->id, ['status' => 'published'])->assertRedirect();
        $this->get('/oleh-oleh/produk/'.$review->product->slug)->assertInertia(fn (Assert $page) => $page->has('reviews', 1)->where('reviews.0.body', $review->body));
    }

    public function test_vendor_can_reply_only_to_their_products_and_response_needs_moderation(): void
    {
        $review = SouvenirReview::factory()->create(['status' => 'published']);
        $vendor = $review->product->vendor;
        app(AccessService::class)->grant($vendor->user, 'vendor_admin');
        $other = SouvenirReview::factory()->create();

        $this->actingAs($vendor->user)->put('/vendor/oleh-oleh/ulasan/'.$other->id, ['vendor_response' => 'Terima kasih.'])->assertNotFound();
        $this->put('/vendor/oleh-oleh/ulasan/'.$review->id, ['vendor_response' => 'Terima kasih atas ulasannya.'])->assertRedirect();
        $this->assertDatabaseHas('souvenir_reviews', ['id' => $review->id, 'vendor_response' => 'Terima kasih atas ulasannya.', 'status' => 'pending']);
    }
}
