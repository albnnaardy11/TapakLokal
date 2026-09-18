<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

Route::get('/blog', function (): Response {
    return Inertia::render('Blog');
})->name('blog');

Route::get('/account', function (): Response {
    return Inertia::render('Account');
})->name('account');

Route::get('/trips/{tripType}/{trip}', function (string $tripType, string $trip): Response {
    abort_unless(in_array($tripType, ['open-trip', 'private-trip'], true), 404);

    return Inertia::render('TripDetail', [
        'tripType' => $tripType,
        'trip' => $trip,
    ]);
})->name('trips.show');

Route::get('/', function () {
    $dbConnected = false;
    $dbError = null;
    try {
        DB::connection()->getPdo();
        $dbConnected = true;
    } catch (Throwable $e) {
        $dbError = $e->getMessage();
    }

    $redisConnected = false;
    $redisError = null;
    try {
        Redis::connection()->ping();
        $redisConnected = true;
    } catch (Throwable $e) {
        $redisError = $e->getMessage();
    }

    return Inertia::render('Welcome', [
        'appName' => config('app.name', 'TapakLokal'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'dbStatus' => [
            'connected' => $dbConnected,
            'driver' => config('database.default'),
            'database' => config('database.connections.'.config('database.default').'.database'),
            'host' => config('database.connections.'.config('database.default').'.host'),
            'port' => config('database.connections.'.config('database.default').'.port'),
            'error' => $dbError,
        ],
        'redisStatus' => [
            'connected' => $redisConnected,
            'client' => config('database.redis.client'),
            'host' => config('database.redis.default.host'),
            'port' => config('database.redis.default.port'),
            'error' => $redisError,
        ],
    ]);
});
