<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Services\AccessService;
use App\Services\AuditService;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirect(Request $request, string $provider = 'google'): RedirectResponse
    {
        abort_unless($provider === 'google', 404, 'Provider tidak didukung.');
        $role = in_array($request->query('role'), ['vendor', 'vendor_admin'], true) ? 'vendor' : ($request->query('role') === 'corporate' ? 'corporate' : 'traveler');
        $request->session()->put('socialite_role', $role);
        $data = $request->validate(['intended' => ['nullable', 'string', 'max:1000', 'regex:~^/(?!/)[^\\\\]*$~'], 'remember' => ['sometimes', 'boolean']]);
        $request->session()->put('socialite_remember', $request->boolean('remember'));
        if (! filled(config("services.{$provider}.client_id")) || ! filled(config("services.{$provider}.client_secret")) || Str::contains(config("services.{$provider}.client_id"), 'your-')) {
            return redirect($request->session()->get('socialite_role') === 'corporate' ? route('corporate.login') : '/?auth=login')->withErrors(['email' => 'Login Google belum dikonfigurasi. Gunakan email dan kata sandi akunmu.']);
        }
        if ($role === 'corporate') {
            $data['intended'] = ($data['intended'] ?? '') === '/corporate/register' ? '/corporate/register' : '/corporate/dashboard';
        }
        $callback = config("services.{$provider}.redirect");
        if (is_string($callback) && filter_var($callback, FILTER_VALIDATE_URL)) {
            $origin = parse_url($callback, PHP_URL_SCHEME).'://'.parse_url($callback, PHP_URL_HOST);
            if ($port = parse_url($callback, PHP_URL_PORT)) {
                $origin .= ':'.$port;
            }
            if ($origin !== $request->getSchemeAndHttpHost()) {
                $intended = $data['intended'] ?? $request->session()->get('url.intended');
                if (is_string($intended) && str_starts_with($intended, $request->getSchemeAndHttpHost().'/')) {
                    $intended = substr($intended, strlen($request->getSchemeAndHttpHost()));
                }
                $query = ['role' => $role];
                if ($request->boolean('remember')) {
                    $query['remember'] = 1;
                }
                if (is_string($intended) && preg_match('~^/(?!/)[^\\\\]*$~', $intended)) {
                    $query['intended'] = $intended;
                }

                return redirect($origin.'/auth/google/redirect?'.http_build_query($query));
            }
        }
        $request->session()->put('socialite_role', $role);
        if (! empty($data['intended'])) {
            $request->session()->put('url.intended', $data['intended']);
        }

        return Socialite::driver($provider)->with(['prompt' => 'select_account'])->redirect();
    }

    /**
     * Handle OAuth callback from provider.
     */
    public function callback(Request $request, AccessService $access, AuditService $audit, string $provider = 'google'): RedirectResponse
    {
        abort_unless(in_array($provider, ['google']), 404, 'Provider tidak didukung.');

        try {
            $socialUser = Socialite::driver($provider)->user();
        } catch (\Throwable $e) {
            Log::warning('auth.oauth.failed', [
                'provider' => $provider,
                'exception_type' => $e::class,
            ]);

            return redirect($request->session()->get('socialite_role') === 'corporate' ? route('corporate.login') : '/?auth=login')->withErrors([
                'email' => 'Login Google gagal diverifikasi. Mulai kembali dan pilih akun Google yang sesuai.',
            ])->with('error', 'Login Google gagal diverifikasi. Mulai kembali dari tombol Google.');
        }

        $email = Str::lower((string) $socialUser->getEmail());
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ! filled($socialUser->getId()) || ($socialUser->getRaw()['email_verified'] ?? false) !== true) {
            return redirect($request->session()->get('socialite_role') === 'corporate' ? route('corporate.login') : '/?auth=login')->withErrors(['email' => 'Identitas Google belum terverifikasi.']);
        }
        $name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'Pengguna Google';
        $googleId = (string) $socialUser->getId();
        $avatar = $socialUser->getAvatar();
        $role = $request->session()->get('socialite_role', 'traveler');

        try {
            return $this->processUserLogin($request, $name, $email, $googleId, $avatar, $role, $provider, $access, $audit);
        } catch (ValidationException $exception) {
            return redirect($request->session()->get('socialite_role') === 'corporate' ? route('corporate.login') : '/?auth=login')->withErrors($exception->errors());
        } catch (QueryException $exception) {
            Log::warning('auth.oauth.persistence_failed', ['provider' => $provider, 'exception_type' => $exception::class]);
            if (Auth::check()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect($role === 'corporate' ? route('corporate.login') : '/?auth=login')->withErrors(['email' => 'Login Google belum dapat diselesaikan. Silakan coba kembali.']);
        }
    }

    /**
     * Helper to process Socialite login/registration.
     */
    protected function processUserLogin(
        Request $request,
        string $name,
        string $email,
        string $googleId,
        ?string $avatar,
        string $role,
        string $provider,
        AccessService $access,
        AuditService $audit
    ): RedirectResponse {
        $linked = User::where('google_id', $googleId)->first();
        $emailUsers = User::whereRaw('lower(email) = ?', [$email])->limit(2)->get();
        if ($emailUsers->count() > 1) {
            throw ValidationException::withMessages(['email' => 'Identitas Google tidak cocok dengan akun tersimpan. Hubungi bantuan untuk pemeriksaan akun.']);
        }
        $byEmail = $emailUsers->first();
        if (($linked && $byEmail && $linked->id !== $byEmail->id) || ($linked && Str::lower($linked->email) !== $email) || ($byEmail && $byEmail->google_id && $byEmail->google_id !== $googleId)) {
            throw ValidationException::withMessages(['email' => 'Identitas Google tidak cocok dengan akun tersimpan. Hubungi bantuan untuk pemeriksaan akun.']);
        }
        $user = $linked ?? $byEmail;
        if ($user && $user->status !== 'active') {
            throw ValidationException::withMessages(['email' => 'Akun ini tidak aktif. Hubungi bantuan.']);
        }

        if (! $user) {
            $user = DB::transaction(function () use ($name, $email, $googleId, $avatar, $role, $access) {
                $user = User::create([
                    'name' => $name,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'email_verified_at' => now(),
                    'status' => 'active',
                ]);

                if (in_array($role, ['vendor', 'vendor_admin'])) {
                    $access->grant($user, 'vendor_admin');
                    Vendor::create([
                        'user_id' => $user->id,
                        'name' => $name,
                        'email' => $email,
                        'phone' => '-',
                        'city' => 'Indonesia',
                        'status' => 'pending',
                    ]);
                } else {
                    $access->grant($user, 'traveler');
                }

                return $user;
            });
        } else {
            // Update socialite metadata if not already set or avatar updated
            $user->update([
                'google_id' => $user->google_id ?? $googleId,
                'avatar' => $avatar ?? $user->avatar,
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);

            // If user already exists as traveler but clicked sign up as vendor and doesn't have vendor role yet
            if (in_array($role, ['vendor', 'vendor_admin']) && ! $user->hasPermission('vendor.access')) {
                $access->grant($user, 'vendor_admin');
                Vendor::firstOrCreate(
                    ['user_id' => $user->id],
                    [
                        'name' => $user->name,
                        'email' => $user->email,
                        'phone' => $user->phone ?? '-',
                        'city' => $user->city ?? 'Indonesia',
                        'status' => 'pending',
                    ]
                );
            }
        }

        Auth::login($user, (bool) $request->session()->pull('socialite_remember', false));
        $request->session()->put('auth_portal', $role === 'corporate' ? 'corporate' : ($user->hasPermission('admin.access') ? 'admin' : ($user->hasPermission('vendor.access') ? 'vendor' : 'traveler')));
        $request->session()->regenerate();
        $audit->record('auth.socialite.'.$provider, $user);

        $request->session()->forget('socialite_role');
        if ($role === 'corporate') {
            if ($user->must_change_password) {
                return to_route('password.change');
            }

            return redirect()->intended(route('corporate.dashboard'));
        }

        $loginSuccessData = [
            'name' => $user->name,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'role' => $user->hasPermission('vendor.access') ? 'Mitra Bisnis' : 'Wisatawan',
            'tier' => $user->tier ?? 'Bronze Priority',
        ];

        if ($user->hasPermission('vendor.access')) {
            return redirect()->intended(route('vendor.dashboard'))->with('login_success_data', $loginSuccessData);
        }

        if ($user->hasPermission('admin.access')) {
            return redirect()->intended(route('admin.dashboard'))->with('login_success_data', $loginSuccessData);
        }

        return redirect()->intended('/')->with('login_success_data', $loginSuccessData);
    }
}
