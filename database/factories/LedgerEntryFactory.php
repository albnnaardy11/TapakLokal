<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\LedgerEntry;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<LedgerEntry> */
class LedgerEntryFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => Booking::factory(), 'reference' => (string) Str::ulid(), 'account' => 'gateway_cash', 'amount' => 500000, 'description' => 'Pembayaran perjalanan'];
    }
}
