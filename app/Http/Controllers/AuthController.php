<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
use App\Services\AuditService;
use App\Services\AccessService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    public function create(Request $request): Response
    {
        if ($request->filled('trip')) {
            $request->validate(['trip' => ['integer']]);
            $trip = \App\Models\Trip::where('status', 'published')->find($request->input('trip'));
            if ($trip) {
                $request->session()->put('url.intended', route('trips.show', [$trip->type, $trip->slug]));
            }
        }
        return Inertia::render('Auth/Login');
    }

    public function store(LoginRequest $request, AuditService $audit): RedirectResponse
    {
        if (! Auth::attempt([...$request->safe()->only(['email', 'password']), 'status' => 'active'], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak sesuai.']);
        }
        $request->session()->regenerate();
        $audit->record('auth.login', $request->user());
        $user = $request->user();
        $route = $user->hasPermission('admin.access') ? 'admin.dashboard' : ($user->hasPermission('vendor.access') ? 'vendor.dashboard' : 'account');
        return redirect()->intended(route($route));
    }

    public function register(RegistrationRequest $request, AccessService $access): RedirectResponse
    {
        $user = DB::transaction(function () use ($request, $access) {
            $user = User::create($request->safe()->only(['name', 'email', 'password']));
            $access->grant($user, 'traveler');
            return $user;
        });
        Auth::login($user);
        $request->session()->regenerate();
        return to_route('account');
    }

    public function destroy(Request $request, AuditService $audit): RedirectResponse
    {
        $audit->record('auth.logout', $request->user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('login');
    }

    public function forgot(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        Password::sendResetLink($request->only('email'));
        return back()->with('success', 'Jika email terdaftar, tautan pemulihan akan dikirim.');
    }

    public function reset(Request $request): RedirectResponse
    {
        $request->validate(['token' => ['required'], 'email' => ['required', 'email'], 'password' => ['required', 'confirmed', \Illuminate\Validation\Rules\Password::min(10)->letters()->numbers()]]);
        $status = Password::reset($request->only('email', 'password', 'password_confirmation', 'token'), function (User $user, string $password) {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            event(new \Illuminate\Auth\Events\PasswordReset($user));
        });
        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }
        return to_route('login')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
