<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\CorporateMembership;
use App\Models\User;
use App\Services\AccessService;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CorporateAuthController extends Controller
{
    public function create(Request $request): Response|RedirectResponse
    {
        $destination = $request->query('next') === 'register' || in_array(parse_url((string) $request->session()->get('url.intended'), PHP_URL_PATH), ['/corporate/start', '/corporate/register'], true) ? 'register' : 'dashboard';
        if ($user = $request->user()) {
            $hasCorporateAccess = CorporateMembership::where('user_id', $user->id)->exists()
                || $user->hasPermission('admin.access');

            if (! $hasCorporateAccess && $destination !== 'register') {
                return to_route('corporate.register');
            }

            return to_route('corporate.'.$destination);
        }

        return Inertia::render('Auth/CorporateLogin', ['destination' => $destination]);
    }

    public function store(LoginRequest $request, AuditService $audit): RedirectResponse
    {
        if (! Auth::attempt([...$request->safe()->only(['email', 'password']), 'status' => 'active'], $request->boolean('remember'))) {
            throw ValidationException::withMessages(['email' => 'Email atau kata sandi tidak sesuai. Gunakan akun yang menerima undangan perusahaan.']);
        }
        $request->session()->regenerate();
        $user = $request->user();

        $isRegistering = $request->input('next') === 'register' || in_array(parse_url((string) $request->session()->get('url.intended'), PHP_URL_PATH), ['/corporate/start', '/corporate/register'], true);
        $hasCorporateAccess = CorporateMembership::where('user_id', $user->id)->exists()
            || $user->hasPermission('admin.access');

        if (! $hasCorporateAccess && ! $isRegistering) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Akses ditolak: Akun ini adalah akun wisatawan dan belum terdaftar di Corporate. Silakan daftarkan perusahaan Anda terlebih dahulu.',
            ]);
        }

        $request->session()->put('auth_portal', 'corporate');
        $audit->record('corporate.auth.login', $user);

        return $this->destination($request);
    }

    public function register(RegistrationRequest $request, AccessService $access, AuditService $audit): RedirectResponse
    {
        $request->validate(['consent' => ['accepted']]);
        $user = DB::transaction(function () use ($request, $access): User {
            $user = User::create($request->safe()->only(['name', 'email', 'password']));
            $access->grant($user, 'traveler');

            return $user;
        });
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('auth_portal', 'corporate');
        $audit->record('corporate.auth.register', $user);

        return $this->destination($request);
    }

    private function destination(Request $request): RedirectResponse
    {
        $hasCorporateAccess = CorporateMembership::where('user_id', $request->user()->id)->exists()
            || $request->user()->hasPermission('admin.access');

        $isRegister = $request->input('next') === 'register' || ! $hasCorporateAccess;
        $destination = route($isRegister ? 'corporate.register' : 'corporate.dashboard');
        $request->session()->forget('url.intended');
        if ($request->user()->must_change_password) {
            $request->session()->put('url.intended', $destination);

            return to_route('password.change');
        }

        return redirect($destination);
    }
}
