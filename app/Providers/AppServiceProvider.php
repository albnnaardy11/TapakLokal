<?php

namespace App\Providers;

use App\Models\ContentPage;
use App\Models\Faq;
use App\Models\Partner;
use App\Models\User;
use App\Observers\PublicContentObserver;
use App\Services\AccessService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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
        foreach ([ContentPage::class, Faq::class, Partner::class] as $model) {
            $model::observe(PublicContentObserver::class);
        }
        foreach (collect((new AccessService)->rolePermissions())->flatten()->unique() as $permission) {
            Gate::define($permission, fn (User $user) => $user->hasPermission($permission));
        }
        RateLimiter::for('login', fn (Request $request) => Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()));
    }
}
