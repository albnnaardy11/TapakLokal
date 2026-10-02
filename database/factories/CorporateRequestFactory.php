<?php

namespace Database\Factories;

use App\Models\CorporateCompany;
use App\Models\CorporateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<CorporateRequest> */
class CorporateRequestFactory extends Factory
{
    public function definition(): array
    {
        return ['corporate_company_id' => CorporateCompany::factory(), 'user_id' => User::factory(), 'reference' => 'COR-'.Str::upper((string) Str::ulid()), 'idempotency_key' => (string) Str::uuid(), 'title' => 'Gathering divisi', 'destination' => 'Yogyakarta', 'departure_date' => now()->addDays(7), 'end_date' => now()->addDays(8), 'participants' => 5, 'budget' => 10000000, 'cost_center' => 'HR', 'needs' => 'Gathering tim dengan transportasi dan konsumsi.', 'travelers' => array_map(fn ($number) => ['name' => 'Peserta '.$number], range(1, 5)), 'status' => 'submitted'];
    }
}
