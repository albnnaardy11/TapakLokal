<?php

namespace Database\Factories;

use App\Models\SouvenirOrder;
use App\Models\SouvenirOrderItem;
use App\Models\SouvenirReview;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SouvenirReview> */
class SouvenirReviewFactory extends Factory
{
    public function definition(): array
    {
        return ['souvenir_order_item_id' => SouvenirOrderItem::factory(), 'souvenir_product_id' => fn (array $attributes) => SouvenirOrderItem::findOrFail($attributes['souvenir_order_item_id'])->souvenir_product_id, 'user_id' => fn (array $attributes) => SouvenirOrder::findOrFail(SouvenirOrderItem::findOrFail($attributes['souvenir_order_item_id'])->souvenir_order_id)->user_id, 'rating' => 5, 'body' => 'Produk sesuai pesanan, kemasan rapi.', 'status' => 'pending'];
    }
}
