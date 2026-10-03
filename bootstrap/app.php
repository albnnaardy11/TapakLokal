<?php

use App\Http\Middleware\CanonicalLocalHost;
use App\Http\Middleware\EnsureActiveUser;
use App\Http\Middleware\EnsurePortalSession;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\Middleware\AuthenticateSession;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->prepend(CanonicalLocalHost::class);
        $middleware->validateCsrfTokens(except: ['payments/midtrans/notification']);
        $middleware->redirectGuestsTo(fn (Request $request) => match (true) {
            $request->is('admin*') => route('admin.login'),
            $request->is('vendor*') => route('vendor.login'),
            $request->is('corporate*') => route('corporate.login'),
            default => route('login'),
        });
        $middleware->web(append: [
            AuthenticateSession::class,
            HandleInertiaRequests::class,
            EnsurePortalSession::class,
            EnsureActiveUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
