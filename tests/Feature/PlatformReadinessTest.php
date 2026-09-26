<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PlatformReadinessTest extends TestCase
{
    public function test_production_check_rejects_local_configuration_and_missing_accounts(): void
    {
        Storage::fake('local');
        config(['app.debug' => true, 'platform.midtrans_server_key' => null]);
        $this->artisan('platform:check', ['--production' => true])->expectsOutputToContain('FAIL')->assertFailed();
        $this->assertCount(0, Storage::disk('local')->allFiles());
    }
}
