<?php

namespace Database\Factories;

use App\Models\Affiliate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Affiliate> */
class AffiliateFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'code' => strtoupper(fake()->unique()->bothify('AFF-????')), 'commission_bps' => 100, 'status' => 'pending'];
    }
}
