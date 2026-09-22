<?php
namespace Database\Factories;

use App\Models\RewardEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<RewardEntry> */
class RewardEntryFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => \App\Models\User::factory(), 'reference' => (string) \Illuminate\Support\Str::ulid(), 'points' => 50, 'description' => 'Perjalanan selesai'];
    }
}

