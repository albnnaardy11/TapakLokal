<?php
namespace Database\Factories;

use App\Models\LedgerEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<LedgerEntry> */
class LedgerEntryFactory extends Factory
{
    public function definition(): array
    {
        return ['booking_id' => \App\Models\Booking::factory(), 'reference' => (string) \Illuminate\Support\Str::ulid(), 'account' => 'gateway_cash', 'amount' => 500000, 'description' => 'Pembayaran perjalanan'];
    }
}

