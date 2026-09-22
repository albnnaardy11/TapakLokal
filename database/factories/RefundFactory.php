<?php
namespace Database\Factories;

use App\Models\Refund;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Refund> */
class RefundFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => \App\Models\Booking::factory(), 'user_id' => fn (array $a) => \App\Models\Booking::findOrFail($a['booking_id'])->user_id, 'amount' => 500000, 'reason' => 'Perjalanan dibatalkan karena keadaan darurat.', 'status' => 'pending'];
    }
}

