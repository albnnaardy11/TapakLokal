<?php

namespace Database\Factories;

use App\Models\CorporateCompany;
use App\Models\CorporateMembership;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<CorporateMembership> */
class CorporateMembershipFactory extends Factory
{
    public function definition(): array
    {
        return ['corporate_company_id' => CorporateCompany::factory(), 'user_id' => User::factory(), 'role' => 'requester', 'status' => 'active'];
    }
}
