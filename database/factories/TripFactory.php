<?php

namespace Database\Factories;

use App\Models\Trip;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Trip> */
class TripFactory extends Factory
{
    public function definition(): array
    {
        return ['vendor_id' => Vendor::factory()->state(['status' => 'verified']), 'title' => fake()->sentence(3), 'slug' => fake()->unique()->slug(3), 'type' => 'open-trip', 'destination' => 'Yogyakarta', 'description' => fake()->paragraph(), 'itinerary' => 'Berkumpul dan menjelajah desa.', 'meeting_point' => 'Stasiun Tugu', 'departure_date' => now()->addDays(7), 'end_date' => now()->addDays(8), 'capacity' => 10, 'reserved_seats' => 0, 'price' => 500000, 'status' => 'published'];
    }
}
