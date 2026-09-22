<?php
namespace Database\Factories;

use App\Models\Payout;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payout> */
class PayoutFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => \App\Models\Booking::factory(), 'vendor_id' => fn (array $a) => \App\Models\Booking::findOrFail($a['booking_id'])->vendor_id, 'amount' => 500000, 'status' => 'eligible'];
    }
}

