<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendor;
use App\Services\AccessService;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    /**
     * Redirect to OAuth provider or simulate in local environment when keys are absent.
     */
    public function redirect(Request $request, string $provider = 'google'): RedirectResponse
    {
        abort_unless(in_array($provider, ['google']), 404, 'Provider tidak didukung.');

        $role = $request->query('role', 'traveler');
        $role = in_array($role, ['vendor', 'vendor_admin']) ? 'vendor' : 'traveler';
        $request->session()->put('socialite_role', $role);

        if ($request->filled('intended')) {
            $request->session()->put('url.intended', $request->query('intended'));
        }

        $clientId = config("services.{$provider}.client_id");
        $clientSecret = config("services.{$provider}.client_secret");

        // If credentials are configured, use official Socialite redirect
        if (! empty($clientId) && ! empty($clientSecret) && ! Str::contains($clientId, 'your-')) {
            return Socialite::driver($provider)
                ->stateless()
                ->with(['prompt' => 'select_account'])
                ->redirect();
        }

        // Local development simulation fallback if OAuth keys are not configured yet
        return $this->handleDevSimulation($request, $provider, $role);
    }

    /**
     * Handle OAuth callback from provider.
     */
    public function callback(Request $request, AccessService $access, AuditService $audit, string $provider = 'google'): RedirectResponse
    {
        abort_unless(in_array($provider, ['google']), 404, 'Provider tidak didukung.');

        try {
            $socialUser = Socialite::driver($provider)->stateless()->user();
        } catch (\Throwable $e) {
            Log::error('Socialite callback failed: '.$e->getMessage(), [
                'provider' => $provider,
                'exception' => $e,
            ]);

            return redirect('/?auth=login')->withErrors([
                'email' => 'Gagal menghubungkan dengan Google: '.$e->getMessage(),
            ])->with('error', 'Gagal menghubungkan dengan Google: '.$e->getMessage());
        }

        $email = $socialUser->getEmail();
        $name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'Pengguna Google';
        $googleId = (string) $socialUser->getId();
        $avatar = $socialUser->getAvatar();
        $role = $request->session()->pull('socialite_role', 'traveler');

        return $this->processUserLogin($request, $name, $email, $googleId, $avatar, $role, $provider, $access, $audit);
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
        $user = User::where('google_id', $googleId)
            ->orWhere('email', $email)
            ->first();

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

        Auth::login($user, true);
        $request->session()->regenerate();
        $audit->record('auth.socialite.'.$provider, $user);

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

    /**
     * Seamless dev simulation when local environment lacks OAuth credentials.
     */
    protected function handleDevSimulation(Request $request, string $provider, string $role): RedirectResponse
    {
        $access = app(AccessService::class);
        $audit = app(AuditService::class);

        $isVendor = in_array($role, ['vendor', 'vendor_admin']);
        $name = $isVendor ? 'Mitra Wisata Nusantara' : 'Traveler Google';
        $email = $isVendor ? 'mitra.google@tapaklokal.test' : 'traveler.google@tapaklokal.test';
        $googleId = $isVendor ? 'google_vendor_dev_1001' : 'google_traveler_dev_1002';
        $avatar = 'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?auto=format&fit=crop&w=150&q=80';

        return $this->processUserLogin($request, $name, $email, $googleId, $avatar, $role, $provider, $access, $audit);
    }
}
