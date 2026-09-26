<?php

namespace Database\Factories;

use App\Models\MediaAsset;
use App\Models\VirtualTour;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<VirtualTour> */
class VirtualTourFactory extends Factory
{
    public function definition(): array
    {
        return ['title' => fake()->sentence(3), 'slug' => fake()->unique()->slug(), 'status' => 'draft', 'placement' => 'homepage', 'media_asset_id' => MediaAsset::factory()->state(['name' => 'panorama.webp', 'mime_type' => 'image/webp']), 'position' => 0];
    }
}
