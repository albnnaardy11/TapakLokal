<?php
namespace Database\Factories;

use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Vendor> */
class VendorFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => \App\Models\User::factory(), 'name' => fake()->company(), 'city' => fake()->city(), 'email' => fake()->safeEmail(), 'phone' => '081234567890', 'status' => 'pending'];
    }
}

