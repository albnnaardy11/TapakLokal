<?php
namespace Database\Factories;

use App\Models\Promotion;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Promotion> */
class PromotionFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => 'Promo lokal', 'code' => strtoupper(fake()->unique()->bothify('TRIP-????')), 'type' => 'percent', 'value' => 10, 'minimum_amount' => 100000, 'usage_limit' => 10, 'used_count' => 0, 'starts_at' => today(), 'ends_at' => today()->addDays(30), 'status' => 'published'];
    }
}

