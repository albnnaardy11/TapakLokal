<?php

namespace Database\Factories;

use App\Models\RewardEntry;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<RewardEntry> */
class RewardEntryFactory extends Factory
{
    public function definition(): array
    {
        return ['user_id' => User::factory(), 'reference' => (string) Str::ulid(), 'points' => 50, 'description' => 'Perjalanan selesai'];
    }
}
