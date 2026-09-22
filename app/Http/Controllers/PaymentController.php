<?php
namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\FinanceService;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Http;
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
        abort_unless(\App\Models\Payment::where('reference', $data['order_id'])->exists(), 404);
        \App\Jobs\ReconcilePayment::dispatch($data['order_id']);
        return response()->json(['received' => true], 202);
    }
}
