<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ReconcileSouvenirPayment;
use App\Models\SouvenirOrder;
use App\Models\SouvenirPayment;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class SouvenirFinanceController extends Controller
{
    public function index(Request $request): Response
    {
        $data = $request->validate(['status' => ['nullable', 'in:pending,paid,failed,reconciliation_required']]);
        $orders = SouvenirOrder::with('payment', 'vendor:id,name')->when($data['status'] ?? null, fn ($q, $status) => $q->whereHas('payment', fn ($payment) => $payment->where('status', $status)))->latest('id')->simplePaginate(20)->withQueryString();

        return Inertia::render('Admin/SouvenirFinance', ['orders' => $orders, 'status' => $data['status'] ?? '', 'ledger' => DB::table('souvenir_ledger_entries')->latest('id')->limit(100)->get(['id', 'souvenir_order_id', 'reference', 'account', 'amount', 'created_at'])]);
    }

    public function reconcile(Request $request, SouvenirPayment $payment, AuditService $audit): RedirectResponse
    {
        abort_unless(config('platform.midtrans_server_key'), 422, 'Midtrans belum dikonfigurasi.');
        ReconcileSouvenirPayment::dispatch($payment->reference);
        $audit->record('souvenir.payment.reconciliation_requested', $payment);

        return back()->with('success', 'Pemeriksaan status Midtrans masuk antrean. Perubahan status menunggu respons gateway yang terverifikasi.');
    }
}
