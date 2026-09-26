<?php

namespace App\Http\Controllers;

use App\Jobs\ReconcilePayment;
use App\Models\Booking;
use App\Models\Payment;
use App\Services\FinanceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function checkout(Request $request, Booking $booking, FinanceService $finance): Response
    {
        abort_unless($booking->user_id === $request->user()->id, 404);

        return Inertia::location($finance->checkout($booking));
    }

    public function webhook(Request $request, FinanceService $finance): JsonResponse
    {
        $data = $request->validate(['order_id' => ['required', 'string', 'max:100'], 'status_code' => ['required', 'string'], 'gross_amount' => ['required', 'string'], 'signature_key' => ['required', 'string']]);
        $key = config('platform.midtrans_server_key');
        abort_unless($key && hash_equals(hash('sha512', $data['order_id'].$data['status_code'].$data['gross_amount'].$key), $data['signature_key']), 403);
        abort_unless(Payment::where('reference', $data['order_id'])->exists(), 404);
        ReconcilePayment::dispatch($data['order_id']);

        return response()->json(['received' => true], 202);
    }
}
