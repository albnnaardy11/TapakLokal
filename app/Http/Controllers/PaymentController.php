<?php

namespace App\Http\Controllers;

use App\Jobs\ReconcilePayment;
use App\Jobs\ReconcilePaymentShare;
use App\Jobs\ReconcileSouvenirPayment;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\PaymentShare;
use App\Models\SouvenirPayment;
use App\Services\CorporateService;
use App\Services\FinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function checkout(Request $request, Booking $booking, FinanceService $finance): Response
    {
        app(CorporateService::class)->authorizeBooking($request->user(), $booking);

        if ($booking->corporateRequest) {
            return to_route('checkout.payment', ['type' => 'trip', 'id' => $booking->id]);
        }

        return Inertia::location($finance->checkout($booking));
    }

    public function webhook(Request $request, FinanceService $finance): JsonResponse
    {
        $data = $request->validate(['order_id' => ['required', 'string', 'max:100'], 'status_code' => ['required', 'string', 'max:3'], 'gross_amount' => ['required', 'string', 'max:32'], 'signature_key' => ['required', 'string', 'max:128']]);
        $key = config('platform.midtrans_server_key');
        abort_unless($key && hash_equals(hash('sha512', $data['order_id'].$data['status_code'].$data['gross_amount'].$key), $data['signature_key']), 403);
        if (PaymentShare::where('reference', $data['order_id'])->exists()) {
            ReconcilePaymentShare::dispatch($data['order_id']);
        } elseif (SouvenirPayment::where('reference', $data['order_id'])->exists()) {
            ReconcileSouvenirPayment::dispatch($data['order_id']);
        } else {
            abort_unless(Payment::where('reference', $data['order_id'])->exists(), 404);
            ReconcilePayment::dispatch($data['order_id']);
        }

        return response()->json(['received' => true], 202);
    }
}
