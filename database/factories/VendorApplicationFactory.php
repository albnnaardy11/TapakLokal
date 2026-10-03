<?php

namespace Database\Factories;

use App\Models\VendorApplication;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<VendorApplication> */
class VendorApplicationFactory extends Factory
{
    public function definition(): array
    {
        return ['track' => 'trip', 'business' => fake()->company(), 'name' => fake()->name(), 'city' => fake()->city(), 'contact' => fake()->safeEmail(), 'notes' => 'Operator wisata lokal', 'status' => 'pending'];
    }
}
