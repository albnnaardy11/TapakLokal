<?php

namespace Database\Seeders;

use App\Models\PlatformSetting;
use App\Services\AccessService;
use Illuminate\Database\Seeder;

class PlatformSeeder extends Seeder
{
    public function run(): void
    {
        (new AccessService)->seed();
        PlatformSetting::firstOrCreate(['key' => 'platform_name'], ['value' => 'Tapak Lokal']);
        if (! app()->environment('testing')) {
            PlatformSetting::firstOrCreate(['key' => 'markup_percent'], ['value' => '10']);
        }
    }
}
