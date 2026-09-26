<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payout;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payout> */
class PayoutFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => Booking::factory(), 'vendor_id' => fn (array $a) => Booking::findOrFail($a['booking_id'])->vendor_id, 'amount' => 500000, 'status' => 'eligible'];
    }
}
