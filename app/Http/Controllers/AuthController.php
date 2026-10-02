<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vendor;
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
    public function create(Request $request): RedirectResponse
    {
        if ($request->filled('return_to')) {
            $data = $request->validate(['return_to' => ['string', 'max:255', 'regex:~^/oleh-oleh(?:/produk/[a-zA-Z0-9-]+|/toko/[0-9]+)?$~']]);
            $request->session()->put('url.intended', $data['return_to']);

            return redirect($data['return_to'].'?auth=login');
        }
        if ($request->filled('trip')) {
            $request->validate(['trip' => ['integer']]);
            $trip = Trip::where('status', 'published')->find($request->input('trip'));
            if ($trip) {
                $request->session()->put('url.intended', route('trips.show', [$trip->type, $trip->slug, 'book' => 1]));

                return redirect()->route('trips.show', [$trip->type, $trip->slug, 'auth' => 'login']);
            }
        }

        return redirect('/?auth=login');
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

        $request->session()->put('auth_portal', 'admin');
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

        $request->session()->put('auth_portal', 'vendor');
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
        $request->session()->put('auth_portal', $request->user()->hasPermission('admin.access') ? 'admin' : ($request->user()->hasPermission('vendor.access') ? 'vendor' : 'traveler'));
        $audit->record('auth.login', $request->user());
        $user = $request->user();

        if ($user->must_change_password) {
            return to_route('password.change');
        }

        $route = $user->hasPermission('admin.access')
            ? route('admin.dashboard')
            : ($user->hasPermission('vendor.access') ? route('vendor.dashboard') : '/');

        $loginSuccessData = [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'role' => $user->hasPermission('vendor.access') ? 'Mitra Bisnis' : 'Wisatawan',
            'tier' => $user->tier ?? 'Bronze Priority',
        ];

        return redirect()->intended($route)->with('login_success_data', $loginSuccessData);
    }

    public function register(RegistrationRequest $request, AccessService $access): RedirectResponse
    {
        $role = $request->input('role', 'traveler');
        $user = DB::transaction(function () use ($request, $access, $role) {
            $user = User::create($request->safe()->only(['name', 'email', 'password']));

            if (in_array($role, ['vendor', 'vendor_admin'])) {
                $access->grant($user, 'vendor_admin');
                Vendor::create([
                    'user_id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? '-',
                    'city' => $user->city ?? 'Indonesia',
                    'status' => 'pending',
                ]);
            } else {
                $access->grant($user, 'traveler');
            }

            return $user;
        });

        Auth::login($user);
        $request->session()->put('auth_portal', $user->hasPermission('vendor.access') ? 'vendor' : 'traveler');
        $request->session()->regenerate();

        $loginSuccessData = [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'role' => $user->hasPermission('vendor.access') ? 'Mitra Bisnis' : 'Wisatawan',
            'tier' => $user->tier ?? 'Bronze Priority',
        ];

        if ($user->hasPermission('vendor.access')) {
            return to_route('vendor.dashboard')->with('login_success_data', $loginSuccessData);
        }

        return redirect()->intended('/')->with('login_success_data', $loginSuccessData);
    }

    public function destroy(Request $request, AuditService $audit): RedirectResponse
    {
        $audit->record('auth.logout', $request->user());
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return match ($request->input('switch_portal')) {
            'corporate' => to_route('corporate.login'),
            'admin' => to_route('admin.login'),
            'vendor' => to_route('vendor.login'),
            'traveler' => to_route('login'),
            default => redirect('/'),
        };
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

        return redirect('/?auth=login')->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
