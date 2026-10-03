<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Promotion;
use App\Models\Trip;
use App\Models\User;
use App\Services\BookingService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia as Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class CheckoutPromotionTest extends TestCase
{
    private function booking(): Booking
    {
        $this->freezeTime();
        config(['platform.markup_bps' => 1000]);

        return app(BookingService::class)->create(User::factory()->create(), ['trip_id' => Trip::factory()->create(['price' => 500000])->id, 'participants' => 1, 'contact_name' => 'Traveler', 'contact_phone' => '08123456789', 'idempotency_key' => (string) Str::uuid()]);
    }

    public function test_promo_updates_order_and_payment_amount_without_trusting_browser_prices(): void
    {
        $booking = $this->booking();
        $promotion = Promotion::factory()->create(['code' => 'HEMAT-TEST', 'type' => 'fixed', 'value' => 20000]);
        $url = route('checkout.promotion', ['type' => 'trip', 'id' => $booking->id]);

        $this->actingAs($booking->user)->put($url, ['promotion_code' => ' hemat-test ', 'discount' => 550000])->assertRedirect();
        $this->put($url, ['promotion_code' => 'HEMAT-TEST'])->assertRedirect();

        $this->assertSame(20000, $booking->fresh()->discount);
        $this->assertSame(530000, $booking->fresh()->total);
        $this->assertSame(30000, $booking->fresh()->platform_fee);
        $this->assertSame(530000, $booking->payment->amount);
        $this->assertSame(1, $promotion->fresh()->used_count);
    }

    public function test_replacing_and_removing_promo_releases_quota_and_restores_total(): void
    {
        $booking = $this->booking();
        $first = Promotion::factory()->create(['type' => 'fixed', 'value' => 20000]);
        $second = Promotion::factory()->create(['type' => 'percent', 'value' => 10]);
        $url = route('checkout.promotion', ['type' => 'trip', 'id' => $booking->id]);

        $this->actingAs($booking->user)->put($url, ['promotion_code' => $first->code])->assertRedirect();
        $this->put($url, ['promotion_code' => $second->code])->assertRedirect();
        $this->assertSame(0, $first->fresh()->used_count);
        $this->assertSame(1, $second->fresh()->used_count);
        $this->assertSame(500000, $booking->fresh()->total);

        $this->put($url, ['promotion_code' => null])->assertRedirect();
        $this->assertSame(0, $second->fresh()->used_count);
        $this->assertNull($booking->fresh()->promotion_id);
        $this->assertSame(550000, $booking->fresh()->total);
        $this->assertSame(550000, $booking->payment->fresh()->amount);
    }

    public static function invalidPromotions(): array
    {
        return [
            'unpublished' => [['status' => 'draft']],
            'quota exhausted' => [['usage_limit' => 1, 'used_count' => 1]],
            'minimum not reached' => [['minimum_amount' => 600000]],
            'expired' => [['ends_at' => '2000-01-01']],
            'not started' => [['starts_at' => '2099-01-01']],
            'no discount' => [['value' => 0]],
        ];
    }

    #[DataProvider('invalidPromotions')]
    public function test_invalid_promo_does_not_change_order_or_payment(array $attributes): void
    {
        $booking = $this->booking();
        $promotion = Promotion::factory()->create($attributes);

        $this->actingAs($booking->user)->put(route('checkout.promotion', ['type' => 'trip', 'id' => $booking->id]), ['promotion_code' => $promotion->code])->assertSessionHasErrors('promotion_code');

        $this->assertNull($booking->fresh()->promotion_id);
        $this->assertSame(550000, $booking->fresh()->total);
        $this->assertSame(550000, $booking->payment->amount);
    }

    public function test_payment_started_or_expired_and_other_users_cannot_modify_promo(): void
    {
        $booking = $this->booking();
        $promotion = Promotion::factory()->create();
        $url = route('checkout.promotion', ['type' => 'trip', 'id' => $booking->id]);
        $this->actingAs(User::factory()->create())->put($url, ['promotion_code' => $promotion->code])->assertNotFound();
        $booking->payment()->update(['method' => 'gopay']);
        $this->actingAs($booking->user)->put($url, ['promotion_code' => $promotion->code])->assertSessionHasErrors('promotion_code');
        $booking->payment()->update(['method' => null]);
        $this->travel(31)->minutes();
        $this->put($url, ['promotion_code' => $promotion->code])->assertSessionHasErrors('promotion_code');
        $this->assertSame(0, $promotion->fresh()->used_count);
    }

    public function test_active_checkout_lock_prevents_changing_payment_total(): void
    {
        $booking = $this->booking();
        $promotion = Promotion::factory()->create();
        $lock = Cache::lock('payment:checkout:'.$booking->id, 90);
        $lock->get();
        try {
            $this->actingAs($booking->user)->put(route('checkout.promotion', ['type' => 'trip', 'id' => $booking->id]), ['promotion_code' => $promotion->code])->assertSessionHasErrors('promotion_code');
            $this->assertSame(550000, $booking->payment->amount);
        } finally {
            $lock->release();
        }
    }

    public function test_checkout_promo_list_uses_actual_discount_and_eligibility(): void
    {
        $booking = $this->booking();
        $promotion = Promotion::factory()->create(['type' => 'fixed', 'value' => 20000]);
        $this->actingAs($booking->user)->get(route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]))
            ->assertOk()->assertInertia(fn (Assert $page) => $page->where('promotions', fn ($items) => collect($items)->contains(fn ($item) => $item['code'] === $promotion->code && $item['discount'] === 20000 && $item['eligible'] === true)));
    }
}
