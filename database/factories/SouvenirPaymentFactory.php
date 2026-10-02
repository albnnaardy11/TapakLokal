<?php

namespace Database\Factories;

use App\Models\SouvenirOrder;
use App\Models\SouvenirPayment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<SouvenirPayment> */
class SouvenirPaymentFactory extends Factory
{
    public function definition(): array
    {
        return ['souvenir_order_id' => SouvenirOrder::factory(), 'reference' => 'SO-'.Str::ulid(), 'amount' => 55000, 'status' => 'pending', 'reconciled_at' => now()];
    }
}
