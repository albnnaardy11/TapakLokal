<?php
namespace Database\Factories;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Campaign> */
class CampaignFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->sentence(3), 'type' => 'campaign', 'description' => fake()->paragraph(), 'starts_at' => today(), 'ends_at' => today()->addMonth(), 'status' => 'draft'];
    }
}

