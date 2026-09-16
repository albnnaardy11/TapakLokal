<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class AuthController extends Controller
{
    /**
     * Show the login page.
     */
    public function showLogin(): Response|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Auth/Login', [
            'status' => session('status'),
            'error' => session('error'),
            'googleConfigured' => ! empty(config('services.google.client_id')),
        ]);
    }

    /**
     * Handle email and password login attempt.
     */
    public function loginWithEmail(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau kata sandi yang Anda masukkan salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Redirect the user to Google OAuth page.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            return redirect()->route('login')->with(
                'error',
                'Kredensial Google OAuth belum disetel di file .env (GOOGLE_CLIENT_ID & GOOGLE_CLIENT_SECRET). Harap isi terlebih dahulu.'
            );
        }

        $redirectUri = config('services.google.redirect') ?: route('auth.google.callback');

        return Socialite::driver('google')
            ->redirectUrl($redirectUri)
            ->redirect();
    }

    /**
     * Handle the callback returned from Google OAuth.
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $redirectUri = config('services.google.redirect') ?: route('auth.google.callback');
            $googleUser = Socialite::driver('google')
                ->redirectUrl($redirectUri)
                ->user();
        } catch (Throwable $e) {
            return redirect()->route('login')->with(
                'error',
                'Gagal masuk menggunakan Google: '.$e->getMessage()
            );
        }

        $email = $googleUser->getEmail();

        if (! $email) {
            return redirect()->route('login')->with(
                'error',
                'Akun Google Anda tidak menyediakan alamat email publik.'
            );
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->getId(),
                'avatar' => $user->avatar ?: $googleUser->getAvatar(),
                'email_verified_at' => $user->email_verified_at ?: now(),
            ]);
        } else {
            $baseUsername = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', explode('@', $email)[0])) ?: 'petualang';
            $uniqueUsername = $baseUsername;
            $counter = 1;

            while (User::where('username', $uniqueUsername)->exists()) {
                $uniqueUsername = $baseUsername.$counter;
                $counter++;
            }

            $user = User::create([
                'name' => $googleUser->getName() ?: 'Petualang TapakLokal',
                'username' => $uniqueUsername,
                'email' => $email,
                'google_id' => $googleUser->getId(),
                'avatar' => $googleUser->getAvatar(),
                'email_verified_at' => now(),
                'role' => 'traveler',
                'member_tier' => 'bronze',
                'points' => 100, // Bonus sambutan untuk pengguna baru
                'wallet_balance' => 0,
                'is_active' => true,
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'))->with(
            'status',
            'Selamat datang kembali, '.$user->name.'!'
        );
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with(
            'status',
            'Anda telah berhasil keluar dari akun.'
        );
    }
}
