<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Review> */
class ReviewFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => Booking::factory()->state(['status' => 'completed']), 'user_id' => fn (array $a) => Booking::findOrFail($a['booking_id'])->user_id, 'trip_id' => fn (array $a) => Booking::findOrFail($a['booking_id'])->trip_id, 'rating' => 5, 'body' => 'Perjalanan menyenangkan bersama pemandu lokal.', 'status' => 'published'];
    }
}
