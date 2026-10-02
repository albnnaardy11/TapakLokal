<?php

namespace Database\Factories;

use App\Models\SouvenirOrder;
use App\Models\SouvenirOrderItem;
use App\Models\SouvenirProduct;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<SouvenirOrderItem> */
class SouvenirOrderItemFactory extends Factory
{
    public function definition(): array
    {
        return ['souvenir_product_id' => SouvenirProduct::factory(), 'souvenir_order_id' => fn (array $attributes) => SouvenirOrder::factory()->create(['vendor_id' => SouvenirProduct::findOrFail($attributes['souvenir_product_id'])->vendor_id, 'status' => 'completed'])->id, 'name' => 'Kopi lokal 250 gram', 'variant' => 'Biji utuh', 'quantity' => 1, 'unit_price' => 55000, 'vendor_price' => 50000];
    }
}
