<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Payment> */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => Booking::factory(), 'reference' => 'PAY-'.Str::ulid(), 'amount' => 500000, 'status' => 'pending'];
    }
}
