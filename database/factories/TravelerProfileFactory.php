<?php
namespace Database\Factories;

use App\Models\TravelerProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<TravelerProfile> */
class TravelerProfileFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => \App\Models\User::factory(), 'name' => fake()->name(), 'phone' => '081234567890'];
    }
}

