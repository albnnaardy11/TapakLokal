<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(PlatformSeeder::class);
        $this->call(AdminAccountSeeder::class);
        $this->call(JawaBaratVendorTripSeeder::class);
        $this->call(WebsiteContentSeeder::class);
        $this->call(VirtualTourSeeder::class);
        $this->call(BrenggoTripSeeder::class);
    }
}
