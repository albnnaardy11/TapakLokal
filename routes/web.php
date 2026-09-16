<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

// Guest Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'loginWithEmail'])->name('login.attempt');
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/account', function (): Response {
    return Inertia::render('Account');
})->name('account');

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
