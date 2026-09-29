<?php

namespace Tests\Feature;

use App\Models\Promotion;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class DiscountPageTest extends TestCase
{
    protected $seed = false;

    public function test_guests_only_see_current_published_promotions_with_available_quota(): void
    {
        $this->travelTo(now()->setTime(23, 59));
        $promotion = Promotion::factory()->create(['starts_at' => today(), 'ends_at' => today()]);
        Promotion::factory()->create(['status' => 'draft']);
        Promotion::factory()->create(['starts_at' => today()->addDay()]);
        Promotion::factory()->create(['ends_at' => today()->subDay()]);
        Promotion::factory()->create(['usage_limit' => 10, 'used_count' => 10]);

        $this->get(route('discount'))->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Discount')
            ->has('promotions', 1)
            ->where('promotions.0.id', $promotion->id)
            ->where('promotions.0.code', $promotion->code));
    }

    public function test_discount_page_is_available_without_promotions(): void
    {
        $this->get(route('discount'))->assertOk()->assertInertia(fn (Assert $page) => $page
            ->component('Discount')->has('promotions', 0));
    }
}
