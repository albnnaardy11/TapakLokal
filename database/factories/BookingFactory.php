<?php
namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Booking> */
class BookingFactory extends Factory
{
    public function definition(): array
    {
        return ['reference' => 'TL-'.\Illuminate\Support\Str::ulid(), 'idempotency_key' => (string) \Illuminate\Support\Str::uuid(), 'user_id' => \App\Models\User::factory(), 'trip_id' => \App\Models\Trip::factory(), 'vendor_id' => fn (array $attributes) => \App\Models\Trip::findOrFail($attributes['trip_id'])->vendor_id, 'participants' => 1, 'contact_name' => fake()->name(), 'contact_phone' => '081234567890', 'subtotal' => 500000, 'total' => 500000, 'platform_fee' => 0, 'vendor_amount' => 500000, 'status' => 'awaiting_payment', 'expires_at' => now()->addMinutes(30)];
    }
}

