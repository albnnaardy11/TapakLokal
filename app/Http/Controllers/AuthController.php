<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\Trip;
use App\Models\User;
use App\Services\AccessService;
use App\Services\AuditService;
use Illuminate\Auth\Events\PasswordReset;
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
            $trip = Trip::where('status', 'published')->find($request->input('trip'));
            if ($trip) {
                $request->session()->put('url.intended', route('trips.show', [$trip->type, $trip->slug]));
            }
        }

        return Inertia::render('Auth/Login');
    }

    public function adminCreate(): Response
    {
        return Inertia::render('Auth/AdminLogin');
    }

    public function adminStore(LoginRequest $request, AuditService $audit): RedirectResponse
    {
        if (! Auth::attempt([...$request->safe()->only(['email', 'password']), 'status' => 'active'], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi admin tidak sesuai.']);
        }
        $request->session()->regenerate();
        $user = $request->user();

        if (! $user->hasPermission('admin.access')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages(['email' => 'Akses ditolak: Akun ini tidak memiliki izin sebagai Administrator Platform.']);
        }

        $audit->record('admin.auth.login', $user);

        if ($user->must_change_password) {
            return to_route('password.change');
        }

        return redirect()->intended(route('admin.dashboard'));
    }

    public function vendorCreate(): Response
    {
        return Inertia::render('Auth/VendorLogin');
    }

    public function vendorStore(LoginRequest $request, AuditService $audit): RedirectResponse
    {
        if (! Auth::attempt([...$request->safe()->only(['email', 'password']), 'status' => 'active'], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi vendor tidak sesuai.']);
        }
        $request->session()->regenerate();
        $user = $request->user();

        if (! $user->hasPermission('vendor.access')) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            throw ValidationException::withMessages(['email' => 'Akses ditolak: Akun ini tidak terdaftar sebagai Mitra Vendor Terverifikasi.']);
        }

        $audit->record('vendor.auth.login', $user);

        if ($user->must_change_password) {
            return to_route('password.change');
        }

        return redirect()->intended(route('vendor.dashboard'));
    }

    public function store(LoginRequest $request, AuditService $audit): RedirectResponse
    {
        if (! Auth::attempt([...$request->safe()->only(['email', 'password']), 'status' => 'active'], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak sesuai.']);
        }
        $request->session()->regenerate();
        $audit->record('auth.login', $request->user());
        $user = $request->user();

        if ($user->must_change_password) {
            return to_route('password.change');
        }

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
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60), 'must_change_password' => false])->save();
            event(new PasswordReset($user));
        });
        if ($status !== Password::PASSWORD_RESET) {
            throw ValidationException::withMessages(['email' => __($status)]);
        }

        return to_route('login')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
