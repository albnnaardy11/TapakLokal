<?php
namespace Database\Factories;

use App\Models\Favorite;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Favorite> */
class FavoriteFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => \App\Models\User::factory(), 'trip_id' => \App\Models\Trip::factory()];
    }
}

