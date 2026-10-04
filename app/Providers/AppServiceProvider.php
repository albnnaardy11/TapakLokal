<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\ContentPage;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\SouvenirOrder;
use App\Models\SupportMessage;
use App\Models\Trip;
use App\Models\User;
use App\Observers\OrderNotificationObserver;
use App\Observers\PublicContentObserver;
use App\Services\AccessService;
use App\Services\PhoneNumberService;
use Illuminate\Auth\Events\Login;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Event::listen(Login::class, function (Login $event): void {
            if ($event->guard === 'web' && request()->hasSession() && $event->user->getAuthPassword()) {
                request()->session()->put('password_hash_web', Auth::guard('web')->hashPasswordForCookie($event->user->getAuthPassword()));
            }
        });
        Booking::observe(OrderNotificationObserver::class);
        SouvenirOrder::observe(OrderNotificationObserver::class);
        SupportMessage::observe(OrderNotificationObserver::class);
        foreach ([ContentPage::class, Faq::class, Partner::class, Trip::class] as $model) {
            $model::observe(PublicContentObserver::class);
        }
        foreach (collect((new AccessService)->rolePermissions())->flatten()->unique() as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
        RateLimiter::for('login', function (Request $request): array {
            $identifier = $request->input('phone', $request->input('email', ''));
            $identifier = is_string($identifier) ? (PhoneNumberService::normalize($identifier) ?? strtolower(trim($identifier))) : '';

            return [
                Limit::perMinute(30)->by('login-ip|'.$request->ip()),
                Limit::perMinute(5)->by('login-identifier|'.hash('sha256', $identifier).'|'.$request->ip()),
            ];
        });
    }
}
