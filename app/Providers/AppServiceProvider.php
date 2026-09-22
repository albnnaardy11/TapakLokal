<?php

namespace App\Providers;

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
        foreach ([\App\Models\ContentPage::class, \App\Models\Faq::class, \App\Models\Partner::class] as $model) {
            $model::observe(\App\Observers\PublicContentObserver::class);
        }
        foreach (collect((new \App\Services\AccessService)->rolePermissions())->flatten()->unique() as $permission) {
            \Illuminate\Support\Facades\Gate::define($permission, fn (\App\Models\User $user) => $user->hasPermission($permission));
        }
        \Illuminate\Support\Facades\RateLimiter::for('login', fn (\Illuminate\Http\Request $request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by(strtolower((string) $request->input('email')).'|'.$request->ip()));
    }
}
