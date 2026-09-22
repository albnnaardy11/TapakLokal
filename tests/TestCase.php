<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use \Illuminate\Foundation\Testing\LazilyRefreshDatabase;

    protected $seed = true;

    protected $seeder = \Database\Seeders\PlatformSeeder::class;
}
