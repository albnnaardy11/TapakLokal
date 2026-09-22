<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{AuditLog, Booking, ContentPage, LedgerEntry, Payment, Payout, Refund, Trip, User, Vendor};
use App\Services\BackofficeRegistry;
use Illuminate\Http\Request;
use Inertia\{Inertia, Response};

class DashboardController extends Controller
{
    public function __invoke(Request $request, BackofficeRegistry $registry): Response
    {
        $user = $request->user();
        $stats = [];
        $alerts = [];
        if ($user->hasPermission('content.view')) {
            $stats[] = ['label' => 'Konten terbit', 'value' => ContentPage::where('status', 'published')->count(), 'hint' => 'Konten website'];
            $alerts[] = ['label' => 'Konten draft', 'count' => ContentPage::where('status', 'draft')->count(), 'url' => route('admin.resources.index', 'blog')];
        }
        if ($user->hasPermission('operations.view')) {
            $stats[] = ['label' => 'Vendor aktif', 'value' => Vendor::where('status', 'verified')->count(), 'hint' => 'Mitra terverifikasi'];
            $stats[] = ['label' => 'Pemesanan', 'value' => Booking::count(), 'hint' => 'Seluruh pemesanan'];
            $stats[] = ['label' => 'Trip terbit', 'value' => Trip::where('status', 'published')->count(), 'hint' => 'Katalog perjalanan'];
            $alerts[] = ['label' => 'Verifikasi vendor', 'count' => Vendor::where('status', 'pending')->count(), 'url' => route('admin.resources.index', 'vendors')];
            $alerts[] = ['label' => 'Moderasi trip', 'count' => Trip::where('status', 'pending')->count(), 'url' => route('admin.resources.index', 'trips')];
        }
        if ($user->hasPermission('finance.view')) {
            $stats[] = ['label' => 'Pembayaran diterima', 'value' => Payment::where('status', 'paid')->sum('amount'), 'money' => true, 'hint' => 'Bukan pendapatan bersih platform'];
            $stats[] = ['label' => 'Saldo ledger', 'value' => LedgerEntry::sum('amount'), 'money' => true, 'hint' => 'Seimbang ketika bernilai nol'];
            $alerts[] = ['label' => 'Refund menunggu', 'count' => Refund::where('status', 'pending')->count(), 'url' => route('admin.resources.index', 'refunds')];
            $alerts[] = ['label' => 'Payout perlu ditinjau', 'count' => Payout::where('status', 'eligible')->count(), 'url' => route('admin.resources.index', 'payouts')];
            $alerts[] = ['label' => 'Perlu rekonsiliasi', 'count' => Payment::where('status', 'reconciliation_required')->count(), 'url' => route('admin.resources.index', 'payments')];
        }
        if ($user->hasPermission('growth.view')) {
            $stats[] = ['label' => 'Promo aktif', 'value' => \App\Models\Promotion::where('status', 'published')->whereDate('starts_at', '<=', today())->whereDate('ends_at', '>=', today())->count(), 'hint' => 'Dalam masa berlaku'];
            $stats[] = ['label' => 'Affiliate aktif', 'value' => \App\Models\Affiliate::where('status', 'active')->count(), 'hint' => 'Program partner'];
        }
        return Inertia::render('Admin/Dashboard', [
            'navigation' => $registry->navigation($user), 'stats' => $stats, 'alerts' => $alerts,
            'activity' => $user->hasPermission('system.view') ? AuditLog::with('user:id,name')->latest('id')->limit(8)->get() : [],
            'gatewayReady' => (bool) config('platform.midtrans_server_key'),
        ]);
    }
}

