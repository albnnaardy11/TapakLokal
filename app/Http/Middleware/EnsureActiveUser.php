<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() && $request->user()->status !== 'active') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(403, 'Akun dinonaktifkan. Hubungi dukungan Tapak Lokal.');
        }

        if ($request->user()?->must_change_password && ! $request->routeIs('password.change', 'account.password', 'logout')) {
            return redirect()->route('password.change');
        }

        return $next($request);
    }
}
