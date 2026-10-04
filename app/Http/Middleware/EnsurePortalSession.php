<?php

namespace App\Http\Middleware;

use App\Models\CorporateRequest;
use Closure;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class EnsurePortalSession
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || $request->routeIs('logout', 'password.change', 'account.password', 'account.logout-other-devices')) {
            return $next($request);
        }
        $current = $request->session()->get('auth_portal', $request->user()->hasPermission('admin.access') ? 'admin' : ($request->user()->hasPermission('vendor.access') ? 'vendor' : 'traveler'));
        $target = $this->target($request);
        if ($target && $target !== $current) {
            if (! $request->isMethod('GET') && ! $request->isMethod('HEAD')) {
                abort(403, 'Keluar dari sesi aktif sebelum masuk ke portal lain.');
            }

            return Inertia::render('Auth/PortalSessionConflict', ['currentPortal' => $current, 'targetPortal' => $target])->toResponse($request)->setStatusCode(403);
        }
        if ($request->routeIs('auth.socialite.redirect')) {
            return redirect()->route(match ($current) {
                'admin' => 'admin.dashboard',
                'vendor' => 'vendor.dashboard',
                'corporate' => 'corporate.dashboard',
                default => 'account',
            });
        }
        if ($request->routeIs('login.store', 'register.store', 'corporate.login.store', 'corporate.account.store', 'admin.login.store', 'vendor.login.store', 'auth.socialite.callback')) {
            abort(403, 'Keluar terlebih dahulu sebelum masuk menggunakan akun lain.');
        }

        return $next($request);
    }

    private function target(Request $request): ?string
    {
        if ($request->routeIs('login') && $request->query('portal') === 'affiliate') {
            return 'affiliate';
        }
        if ($request->is('corporate', 'corporate/*')) {
            return 'corporate';
        }
        if ($request->is('admin', 'admin/*')) {
            return 'admin';
        }
        if ($request->is('vendor', 'vendor/*')) {
            return 'vendor';
        }
        if ($request->routeIs('auth.socialite.redirect', 'auth.socialite.callback')) {
            $role = $request->routeIs('auth.socialite.callback') ? $request->session()->get('socialite_role') : $request->query('role');

            return $role === 'corporate' ? 'corporate' : (in_array($role, ['vendor', 'vendor_admin'], true) ? 'vendor' : 'traveler');
        }
        if ($request->is('checkout/trip/*') && ctype_digit((string) $request->segment(3))) {
            return CorporateRequest::where('booking_id', $request->segment(3))->exists() ? 'corporate' : 'traveler';
        }
        if ($request->is('bookings/*') && ctype_digit((string) $request->segment(2))) {
            return CorporateRequest::where('booking_id', $request->segment(2))->exists() ? 'corporate' : 'traveler';
        }
        if ($request->routeIs('account.password', 'account.payment-methods')) {
            return null;
        }
        if ($request->is('account', 'account/*', 'login', 'register', 'bookings', 'checkout/*', 'favorites/*', 'reviews') || ($request->is('/') && in_array($request->query('auth'), ['login', 'register'], true))) {
            return 'traveler';
        }

        return null;
    }
}
