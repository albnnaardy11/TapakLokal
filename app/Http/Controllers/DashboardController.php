<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Show the user dashboard.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Dashboard', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username ?: '@petualang',
                'email' => $user->email,
                'avatar' => $user->avatar,
                'phone' => $user->phone,
                'role' => $user->role ?? 'traveler',
                'member_tier' => $user->member_tier ?? 'bronze',
                'points' => (int) ($user->points ?? 0),
                'wallet_balance' => (float) ($user->wallet_balance ?? 0),
                'email_verified_at' => $user->email_verified_at,
                'google_id' => $user->google_id,
                'created_at' => $user->created_at?->format('d M Y') ?? date('d M Y'),
            ],
            'recentActivities' => [
                [
                    'id' => 1,
                    'title' => 'Bergabung dengan TapakLokal',
                    'date' => 'Baru saja',
                    'type' => 'Akun',
                    'status' => 'Selesai',
                    'badge' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                ],
                [
                    'id' => 2,
                    'title' => 'Bonus Pendaftaran TapakPoints (+100 Poin)',
                    'date' => 'Hari ini',
                    'type' => 'Reward',
                    'status' => 'Berhasil',
                    'badge' => 'bg-amber-50 text-amber-700 border-amber-200',
                ],
            ],
        ]);
    }
}
