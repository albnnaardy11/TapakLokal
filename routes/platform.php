<?php

use App\Http\Controllers\Admin\AccessController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ResourceController;
use App\Http\Controllers\Admin\VirtualTourController;
use App\Http\Controllers\Admin\WorkflowController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\TravelerController;
use App\Http\Controllers\Vendor;
use App\Http\Middleware\ResolveAdminPanel;
use App\Services\AdminPanelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->middleware('throttle:login')->name('login.store');
    Route::get('/admin/login', [AuthController::class, 'adminCreate'])->name('admin.login');
    Route::post('/admin/login', [AuthController::class, 'adminStore'])->middleware('throttle:login')->name('admin.login.store');
    Route::get('/vendor/login', [AuthController::class, 'vendorCreate'])->name('vendor.login');
    Route::post('/vendor/login', [AuthController::class, 'vendorStore'])->middleware('throttle:login')->name('vendor.login.store');
    Route::get('/register', fn () => Inertia::render('Auth/Login', ['mode' => 'register']))->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1')->name('register.store');
    Route::get('/forgot-password', fn () => Inertia::render('Auth/Login', ['mode' => 'forgot']))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgot'])->middleware('throttle:5,1')->name('password.email');
    Route::get('/reset-password/{token}', fn (string $token) => Inertia::render('Auth/Login', ['mode' => 'reset', 'token' => $token, 'email' => request('email')]))->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');
});
Route::get('/jelajah', [BookingController::class, 'catalog'])->name('catalog');
Route::get('/media/{media}', [MediaController::class, 'show'])->name('media.show');
Route::get('/virtual-tours/{tour}/image', [VirtualTourController::class, 'image'])->whereNumber('tour')->name('tours.image');
Route::post('/payments/midtrans/notification', [PaymentController::class, 'webhook'])->middleware('throttle:120,1')->name('payments.webhook');

Route::middleware('auth')->group(function () {
    Route::get('/password/change', fn () => Inertia::render('Auth/ChangePassword'))->name('password.change');
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
    Route::get('/', fn (Request $request, AdminPanelService $panels) => to_route('admin.panel.dashboard', ['panel' => $panels->home($request->user())]))->middleware('can:dashboard.view')->name('dashboard');
    Route::middleware(ResolveAdminPanel::class)->group(function () {
        Route::prefix('content/virtual-tours')->name('tours.')->group(function () {
            Route::get('/', [VirtualTourController::class, 'index'])->name('index');
            Route::get('/targets', [VirtualTourController::class, 'targets'])->name('targets');
            Route::post('/', [VirtualTourController::class, 'save'])->middleware('throttle:10,1')->name('store');
            Route::post('/{tour}', [VirtualTourController::class, 'save'])->whereNumber('tour')->middleware('throttle:10,1')->name('update');
            Route::get('/{tour}/preview', [VirtualTourController::class, 'preview'])->whereNumber('tour')->name('preview');
        });
        Route::get('/users', [AccessController::class, 'users'])->name('access.users');
        Route::post('/users', [AccessController::class, 'saveUser'])->name('access.users.save');
        Route::get('/roles', [AccessController::class, 'roles'])->name('access.roles');
        Route::put('/roles/{role}', [AccessController::class, 'saveRole'])->name('access.roles.save');
        Route::get('/modules/{module}', [ResourceController::class, 'index'])->name('resources.index');
        Route::post('/modules/{module}', [ResourceController::class, 'store'])->name('resources.store');
        Route::get('/modules/{module}/{record}', [ResourceController::class, 'show'])->whereNumber('record')->name('resources.show');
        Route::put('/modules/{module}/{record}', [ResourceController::class, 'update'])->whereNumber('record')->name('resources.update');
        Route::delete('/modules/{module}/{record}', [ResourceController::class, 'destroy'])->whereNumber('record')->name('resources.destroy');
        Route::post('/modules/{module}/{record}/{action}', WorkflowController::class)->whereNumber('record')->name('workflow');
    });
    Route::prefix('{panel}')->whereIn('panel', ['super', 'content', 'operations', 'finance', 'growth'])->middleware(ResolveAdminPanel::class)->name('panel.')->group(function () {
        Route::get('/', DashboardController::class)->middleware('can:dashboard.view')->name('dashboard');
        Route::get('/modules/{module}', [ResourceController::class, 'index'])->name('resources.index');
        Route::post('/modules/{module}', [ResourceController::class, 'store'])->name('resources.store');
        Route::get('/modules/{module}/{record}', [ResourceController::class, 'show'])->whereNumber('record')->name('resources.show');
        Route::put('/modules/{module}/{record}', [ResourceController::class, 'update'])->whereNumber('record')->name('resources.update');
        Route::delete('/modules/{module}/{record}', [ResourceController::class, 'destroy'])->whereNumber('record')->name('resources.destroy');
        Route::post('/modules/{module}/{record}/{action}', WorkflowController::class)->whereNumber('record')->name('workflow');
    });
});
Route::prefix('vendor')->name('vendor.')->middleware(['auth', 'can:vendor.access'])->group(function () {
    Route::get('/', [Vendor\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/trips/create', [Vendor\TripController::class, 'create'])->name('trips.create');
    Route::post('/trips', [Vendor\TripController::class, 'store'])->name('trips.store');
    Route::get('/trips/{trip}/edit', [Vendor\TripController::class, 'edit'])->name('trips.edit');
    Route::put('/trips/{trip}', [Vendor\TripController::class, 'update'])->name('trips.update');
    Route::delete('/trips/{trip}', [Vendor\TripController::class, 'destroy'])->name('trips.destroy');
    Route::put('/bookings/{booking}', [Vendor\BookingController::class, 'update'])->name('bookings.update');
    Route::put('/profile', [Vendor\DashboardController::class, 'saveProfile'])->name('profile.save');
    Route::get('/{section}', [Vendor\DashboardController::class, 'index'])->name('section');
});
