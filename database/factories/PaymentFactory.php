<?php
namespace Database\Factories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => \App\Models\Booking::factory(), 'reference' => 'PAY-'.\Illuminate\Support\Str::ulid(), 'amount' => 500000, 'status' => 'pending'];
    }
}

