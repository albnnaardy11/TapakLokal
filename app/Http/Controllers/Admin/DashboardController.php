<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Affiliate;
use App\Models\AuditLog;
use App\Models\Booking;
use App\Models\ContentPage;
use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Promotion;
use App\Models\Refund;
use App\Models\Trip;
use App\Models\Vendor;
use App\Services\AdminPanelService;
use App\Services\BackofficeRegistry;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request, BackofficeRegistry $registry): Response
    {
        $user = $request->user();
        $panel = $request->attributes->get('admin_panel');
        $request->validate(['days' => ['nullable', 'integer', Rule::in([7, 30, 90])]]);
        $days = $request->integer('days', 30);
        $stats = [];
        $alerts = [];
        if (in_array($panel, ['super', 'content'], true) && $user->hasPermission('content.view')) {
            $stats[] = ['label' => 'Konten terbit', 'value' => ContentPage::where('status', 'published')->count(), 'hint' => 'Konten website'];
            $alerts[] = ['label' => 'Konten draft', 'count' => ContentPage::where('status', 'draft')->count(), 'url' => $registry->url('blog')];
        }
        if (in_array($panel, ['super', 'operations'], true) && $user->hasPermission('operations.view')) {
            $stats[] = ['label' => 'Vendor aktif', 'value' => Vendor::where('status', 'verified')->count(), 'hint' => 'Mitra terverifikasi'];
            $stats[] = ['label' => 'Pemesanan', 'value' => Booking::count(), 'hint' => 'Seluruh pemesanan'];
            $stats[] = ['label' => 'Trip terbit', 'value' => Trip::where('status', 'published')->count(), 'hint' => 'Katalog perjalanan'];
            $alerts[] = ['label' => 'Verifikasi vendor', 'count' => Vendor::where('status', 'pending')->count(), 'url' => $registry->url('vendors')];
            $alerts[] = ['label' => 'Moderasi trip', 'count' => Trip::where('status', 'pending')->count(), 'url' => $registry->url('trips')];
        }
        if (in_array($panel, ['super', 'finance'], true) && $user->hasPermission('finance.view')) {
            $stats[] = ['label' => 'Pembayaran diterima', 'value' => Payment::where('status', 'paid')->sum('amount'), 'money' => true, 'hint' => 'Bukan pendapatan bersih platform'];
            $stats[] = ['label' => 'Saldo ledger', 'value' => LedgerEntry::sum('amount'), 'money' => true, 'hint' => 'Seimbang ketika bernilai nol'];
            $alerts[] = ['label' => 'Refund menunggu', 'count' => Refund::where('status', 'pending')->count(), 'url' => $registry->url('refunds')];
            $alerts[] = ['label' => 'Payout perlu ditinjau', 'count' => Payout::where('status', 'eligible')->count(), 'url' => $registry->url('payouts')];
            $alerts[] = ['label' => 'Perlu rekonsiliasi', 'count' => Payment::where('status', 'reconciliation_required')->count(), 'url' => $registry->url('payments')];
        }
        if (in_array($panel, ['super', 'growth'], true) && $user->hasPermission('growth.view')) {
            $stats[] = ['label' => 'Promo aktif', 'value' => Promotion::where('status', 'published')->whereDate('starts_at', '<=', today())->whereDate('ends_at', '>=', today())->count(), 'hint' => 'Dalam masa berlaku'];
            $stats[] = ['label' => 'Affiliate aktif', 'value' => Affiliate::where('status', 'active')->count(), 'hint' => 'Program partner'];
        }

        return Inertia::render('Admin/Dashboard', [
            'panelLabel' => (new AdminPanelService)->definitions()[$panel]['label'],
            'panelDescription' => (new AdminPanelService)->definitions()[$panel]['description'],
            'reportDays' => $days,
            'bookingReport' => in_array($panel, ['super', 'finance'], true) && $user->hasPermission('finance.view')
                ? Booking::where('created_at', '>=', now()->subDays($days)->startOfDay())->selectRaw('status, count(*) as bookings, sum(total) as total, sum(platform_fee) as platform_fee, sum(vendor_amount) as vendor_amount')->groupBy('status')->orderBy('status')->get() : [],
            'navigation' => $registry->navigation($user), 'stats' => $stats, 'alerts' => $alerts,
            'activity' => $user->hasPermission('system.view') ? AuditLog::with('user:id,name')->latest('id')->limit(8)->get() : [],
            'gatewayReady' => (bool) config('platform.midtrans_server_key'),
        ]);
    }
}
