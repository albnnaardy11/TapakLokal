<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\PaymentShare;
use App\Services\BookingService;
use App\Services\SplitPaymentService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SplitPaymentController extends Controller
{
    public function ownerStatus(Request $request, Booking $booking, SplitPaymentService $service): JsonResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 404);
        $share = PaymentShare::where('booking_id', $booking->id)->whereIn('status', ['pending', 'expired', 'cancelled', 'failed'])->orderByRaw('CASE WHEN reconciled_at IS NULL THEN 0 ELSE 1 END')->orderBy('reconciled_at')->orderBy('id')->first();
        try {
            if ($share) {
                $service->check($share);
            }
        } catch (ConnectionException $exception) {
            report($exception);

            return response()->json(['message' => 'Status belum dapat diperiksa. Coba lagi.'], 503);
        }

        return response()->json(['payment' => $booking->payment()->first(), 'status' => $booking->fresh()->status]);
    }

    public function create(Request $request, Booking $booking, SplitPaymentService $service): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 404);
        $data = $request->validate(['method' => ['required', 'string', 'max:30']]);
        $service->create($booking, $data['method']);

        return to_route('checkout.payment', ['type' => 'trip', 'id' => $booking->id])->with('success', 'Tagihan dibagi per anggota. Siapkan instruksi dan bagikan link masing-masing.');
    }

    public function charge(Request $request, Booking $booking, PaymentShare $share, SplitPaymentService $service): JsonResponse
    {
        abort_unless($booking->user_id === $request->user()->id && $share->booking_id === $booking->id, 404);
        try {
            $service->charge($share);
        } catch (ConnectionException $exception) {
            report($exception);

            return response()->json(['message' => 'Midtrans belum merespons. Coba lagi pada tagihan yang sama.'], 503);
        }

        return response()->json(['shares' => $service->listing($booking)]);
    }

    public function show(string $token): Response
    {
        $share = $this->find($token);

        return Inertia::render('SplitPayment', $this->data($share));
    }

    public function check(string $token, SplitPaymentService $service): JsonResponse
    {
        $share = $this->find($token);
        try {
            if (in_array($share->status, ['pending', 'expired', 'cancelled', 'failed'], true)) {
                $service->check($share);
            }
        } catch (ConnectionException $exception) {
            report($exception);

            return response()->json(['message' => 'Status belum dapat diperiksa. Coba lagi.'], 503);
        }

        return response()->json($this->data($share->fresh()));
    }

    private function find(string $token): PaymentShare
    {
        abort_unless(preg_match('/^[a-zA-Z0-9]{64}$/', $token), 404);
        $share = PaymentShare::where('token', $token)->firstOrFail();
        $booking = $share->booking;
        if ($booking->status === 'awaiting_payment' && $booking->expires_at->isPast()) {
            app(BookingService::class)->transition($booking, 'expired');
        }
        if (in_array($booking->fresh()->status, ['expired', 'cancelled'], true)) {
            app(SplitPaymentService::class)->close($booking->fresh());
            $share->refresh();
        }

        return $share;
    }

    /** @return array<string, mixed> */
    private function data(PaymentShare $share): array
    {
        return ['share' => $share->only(['label', 'amount', 'status', 'method', 'instructions', 'reference']), 'tripTitle' => $share->booking->trip->title, 'expiresAt' => $share->booking->expires_at, 'bookingStatus' => $share->booking->status, 'checkUrl' => route('split.status', $share->token)];
    }
}
