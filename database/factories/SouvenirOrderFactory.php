<?php

namespace Database\Factories;

use App\Models\SouvenirOrder;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SouvenirOrder> */
class SouvenirOrderFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(), 'vendor_id' => Vendor::factory(),
            'reference' => 'SO-'.Str::ulid(), 'idempotency_key' => fake()->uuid(), 'request_hash' => hash('sha256', 'test'),
            'contact_name' => fake()->name(), 'contact_phone' => '081234567890', 'method' => 'pickup', 'address' => 'Jalan Mitra Lokal nomor 10, Semarang',
            'pickup_date' => now()->addDays(2)->toDateString(), 'subtotal' => 55000, 'total' => 55000, 'vendor_amount' => 50000, 'platform_fee' => 5000,
            'status' => 'awaiting_payment', 'expires_at' => now()->addMinutes(30),
        ];
    }
}
