<?php

use App\Http\Controllers\{AuthController, BookingController, MediaController, PaymentController, TravelerController};
use App\Http\Controllers\Admin\{AccessController, DashboardController, ResourceController, WorkflowController};
use App\Http\Controllers\Vendor;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');
    Route::get('/register', fn () => Inertia::render('Auth/Login', ['mode' => 'register']))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
    Route::get('/forgot-password', fn () => Inertia::render('Auth/Login', ['mode' => 'forgot']))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgot'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', fn (string $token) => Inertia::render('Auth/Login', ['mode' => 'reset', 'token' => $token, 'email' => request('email')]))->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
});
Route::get('/jelajah', [BookingController::class, 'catalog'])->name('catalog');
Route::get('/media/{media}', [MediaController::class, 'show'])->name('media.show');
Route::post('/payments/midtrans/notification', [PaymentController::class, 'webhook'])->middleware('throttle:120,1')->name('payments.webhook');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');
    Route::post('/media', [MediaController::class, 'store'])->middleware('throttle:20,1')->name('media.store');
    Route::get('/account', [TravelerController::class, 'index'])->name('account');
    Route::get('/account/{section}', [TravelerController::class, 'index'])->name('account.section');
    Route::patch('/account/profile', [TravelerController::class, 'profile'])->name('account.profile');
    Route::put('/account/password', [TravelerController::class, 'password'])->middleware('throttle:5,1')->name('account.password');
    Route::post('/account/travelers', [TravelerController::class, 'traveler'])->name('travelers.store');
    Route::patch('/account/travelers/{traveler}', [TravelerController::class, 'traveler'])->name('travelers.update');
    Route::delete('/account/travelers/{traveler}', [TravelerController::class, 'removeTraveler'])->name('travelers.destroy');
    Route::post('/favorites/{trip}', [TravelerController::class, 'favorite'])->name('favorites.store');
    Route::delete('/favorites/{favorite}', [TravelerController::class, 'removeFavorite'])->name('favorites.destroy');
    Route::post('/reviews', [TravelerController::class, 'review'])->name('reviews.store');
    Route::post('/support', [TravelerController::class, 'ticket'])->name('support.store');
    Route::get('/support/{ticket}', [TravelerController::class, 'conversation'])->name('support.show');
    Route::post('/support/{ticket}/messages', [TravelerController::class, 'reply'])->middleware('throttle:30,1')->name('support.reply');
    Route::post('/bookings', [BookingController::class, 'store'])->middleware('throttle:20,1')->name('bookings.store');
    Route::get('/bookings/{booking}', [BookingController::class, 'show'])->name('bookings.show');
    Route::post('/bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');
    Route::post('/bookings/{booking}/refund', [BookingController::class, 'refund'])->name('bookings.refund');
    Route::post('/bookings/{booking}/checkout', [PaymentController::class, 'checkout'])->middleware('throttle:5,1')->name('bookings.checkout');
});
Route::prefix('admin')->name('admin.')->middleware(['auth', 'can:admin.access'])->group(function () {
    Route::get('/', DashboardController::class)->middleware('can:dashboard.view')->name('dashboard');
    Route::get('/users', [AccessController::class, 'users'])->name('access.users');
    Route::post('/users', [AccessController::class, 'saveUser'])->name('access.users.save');
    Route::get('/roles', [AccessController::class, 'roles'])->name('access.roles');
    Route::put('/roles/{role}', [AccessController::class, 'saveRole'])->name('access.roles.save');
    Route::get('/modules/{module}', [ResourceController::class, 'index'])->name('resources.index');
    Route::post('/modules/{module}', [ResourceController::class, 'store'])->name('resources.store');
    Route::get('/modules/{module}/{record}', [ResourceController::class, 'show'])->whereNumber('record')->name('resources.show');
    Route::put('/modules/{module}/{record}', [ResourceController::class, 'update'])->whereNumber('record')->name('resources.update');
    Route::post('/modules/{module}/{record}/{action}', WorkflowController::class)->whereNumber('record')->name('workflow');
});
Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'can:vendor.access'])->group(function () {
    Route::get('/', [Vendor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/trips/create', [Vendor\TripController::class, 'create'])->name('trips.create');
    Route::post('/trips', [Vendor\TripController::class, 'store'])->name('trips.store');
    Route::get('/trips/{trip}/edit', [Vendor\TripController::class, 'edit'])->name('trips.edit');
    Route::put('/trips/{trip}', [Vendor\TripController::class, 'update'])->name('trips.update');
    Route::put('/bookings/{booking}', [Vendor\BookingController::class, 'update'])->name('bookings.update');
    Route::put('/profile', [Vendor\DashboardController::class, 'saveProfile'])->name('profile.save');
    Route::get('/{section}', [Vendor\DashboardController::class, 'index'])->name('section');
});

