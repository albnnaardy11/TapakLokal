<?php

namespace Tests;

use Database\Seeders\PlatformSeeder;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use LazilyRefreshDatabase;

    protected $seed = true;

    protected $seeder = PlatformSeeder::class;
}
