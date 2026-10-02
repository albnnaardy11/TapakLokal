<?php

namespace Database\Factories;

use App\Models\CorporateCompany;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CorporateCompany> */
class CorporateCompanyFactory extends Factory
{
    public function definition(): array
    {
        return ['owner_id' => User::factory(), 'name' => fake()->company(), 'pic_name' => fake()->name(), 'position' => 'HR', 'work_email' => fake()->safeEmail(), 'phone' => '081234567890', 'budget_range' => '10m-50m', 'source' => 'search', 'status' => 'pending', 'consented_at' => now(), 'marketing_consent' => false];
    }
}
