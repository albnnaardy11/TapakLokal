<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return ['reference' => 'TL-'.Str::ulid(), 'idempotency_key' => (string) Str::uuid(), 'user_id' => User::factory(), 'trip_id' => Trip::factory(), 'vendor_id' => fn (array $attributes) => Trip::findOrFail($attributes['trip_id'])->vendor_id, 'participants' => 1, 'contact_name' => fake()->name(), 'contact_phone' => '081234567890', 'subtotal' => 500000, 'total' => 500000, 'platform_fee' => 0, 'vendor_amount' => 500000, 'status' => 'awaiting_payment', 'expires_at' => now()->addMinutes(30)];
    }
}
