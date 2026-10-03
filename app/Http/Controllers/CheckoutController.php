<?php

namespace App\Http\Controllers;

use App\Jobs\ReconcilePayment;
use App\Jobs\ReconcileSouvenirPayment;
use App\Models\Booking;
use App\Models\CorporateRequest;
use App\Models\SouvenirCartItem;
use App\Models\SouvenirOrder;
use App\Models\TravelerProfile;
use App\Models\Trip;
use App\Services\BookingService;
use App\Services\CorporateService;
use App\Services\PaymentInstructionService;
use App\Services\PaymentMethodService;
use App\Services\SouvenirCommerceService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use RuntimeException;

class CheckoutController extends Controller
{
    public function reviewTrip(Request $request, Trip $trip): Response
    {
        $data = $request->validate(['participants' => ['nullable', 'integer', 'min:1', 'max:50']]);
        $trip->load('vendor:id,name,status');
        abort_unless($trip->status === 'published' && $trip->vendor->status === 'verified', 404);

        return Inertia::render('CheckoutReview', [
            'kind' => 'trip', 'trip' => $trip, 'participants' => $data['participants'] ?? 1,
            'canBook' => $trip->departure_date->greaterThanOrEqualTo(today()),
            'bookingKey' => (string) Str::uuid(), 'travelerProfiles' => TravelerProfile::where('user_id', $request->user()->id)->limit(50)->get(),
        ]);
    }

    public function reviewSouvenir(Request $request, int $vendor): Response
    {
        $lines = SouvenirCartItem::where('user_id', $request->user()->id)->whereHas('product', fn ($query) => $query->where('vendor_id', $vendor))->with('product.vendor:id,name,status')->limit(50)->get();
        abort_if($lines->isEmpty(), 404);
        $lines->each(fn ($line) => $line->product->setAttribute('selling_price', $line->product->sellingPrice()));

        return Inertia::render('CheckoutReview', ['kind' => 'souvenir', 'cartLines' => $lines, 'minimumPickup' => today()->addDays($lines->max(fn ($line) => $line->product->preparation_days))->toDateString(), 'bookingKey' => (string) Str::uuid()]);
    }

    public function payment(Request $request, string $type, int $id, PaymentMethodService $methods): Response
    {
        $order = $this->ownedOrder($request, $type, $id);
        $this->expireOrder($order);
        $order->refresh();
        $order->load($type === 'trip' ? ['trip', 'vendor:id,name', 'payment'] : ['items', 'vendor:id,name', 'payment']);

        return Inertia::render('CheckoutPayment', [
            'corporateWorkspaceUrl' => $type === 'trip' && ($corporate = CorporateRequest::where('booking_id', $order->id)->first()) ? route('corporate.workspace', $corporate->corporate_company_id) : null, 'kind' => $type, 'order' => $order, 'paymentMethods' => $methods->catalog(), 'paymentPreferences' => $methods->preferences($request->user()),
            'gatewayReady' => filled(config('platform.midtrans_server_key')), 'serverNow' => now()->toIso8601String(),
            'promotions' => $order instanceof Booking ? app(BookingService::class)->availablePromotions($order) : [],
            'appliedPromotion' => $order instanceof Booking ? $order->promotion()->first(['code', 'name']) : null,
        ]);
    }

    public function charge(Request $request, string $type, int $id, PaymentInstructionService $instructions): RedirectResponse
    {
        $order = $this->ownedOrder($request, $type, $id);
        $data = $request->validate(['method' => ['required', 'string', 'max:30']]);
        $instructions->start($order, $data['method']);

        return to_route('checkout.payment', ['type' => $type, 'id' => $id]);
    }

    public function check(Request $request, string $type, int $id): RedirectResponse|JsonResponse
    {
        $order = $this->ownedOrder($request, $type, $id);
        if (! config('platform.midtrans_server_key')) {
            return $request->expectsJson() ? response()->json(['message' => 'Pembayaran online belum aktif.'], 503) : back()->with('error', 'Pembayaran online belum aktif.');
        }
        try {
            if ($order->payment?->status === 'pending') {
                if ($type === 'trip') {
                    ReconcilePayment::dispatch($order->reference);
                } else {
                    ReconcileSouvenirPayment::dispatch($order->reference);
                }
            }
        } catch (ConnectionException|RuntimeException $exception) {
            report($exception);
            $message = 'Midtrans belum dapat dihubungi. Pesanan tetap tersimpan; coba periksa status lagi nanti.';

            return $request->expectsJson() ? response()->json(['message' => $message], 503) : back()->with('error', $message);
        }

        $order->refresh();
        $this->expireOrder($order);
        if ($request->expectsJson()) {
            return response()->json(['status' => $order->fresh()->status, 'payment' => $order->payment()->first(), 'serverNow' => now()->toIso8601String()]);
        }

        if ($request->boolean('automatic')) {
            return back();
        }

        return back()->with('success', 'Pemeriksaan pembayaran diminta. Status diperbarui setelah verifikasi penyedia.');
    }

    public function preferences(Request $request, PaymentMethodService $methods): RedirectResponse
    {
        $data = $request->validate(['saved' => ['present', 'array', 'max:6'], 'saved.*' => ['required', 'string', 'distinct', 'max:30'], 'primary' => ['nullable', 'string', 'max:30']]);
        $methods->save($request->user(), $data['saved'], $data['primary'] ?? null);

        return back()->with('success', 'Preferensi pembayaran tersimpan.');
    }

    public function promotion(Request $request, string $type, int $id, BookingService $service): RedirectResponse
    {
        $order = $this->ownedOrder($request, $type, $id);
        abort_unless($order instanceof Booking, 422, 'Promo saat ini tersedia untuk perjalanan.');
        abort_if($order->corporateRequest()->exists(), 422, 'Promo perusahaan dikelola melalui workspace.');
        $data = $request->validate(['promotion_code' => ['nullable', 'string', 'max:50']]);
        $service->updatePromotion($order, $data['promotion_code'] ?? null);

        return to_route('checkout.payment', ['type' => $type, 'id' => $id])->with('success', filled($data['promotion_code'] ?? null) ? 'Promo diterapkan. Total pembayaran sudah diperbarui.' : 'Promo dihapus dari pesanan.');
    }

    public function cancel(Request $request, string $type, int $id): RedirectResponse
    {
        $order = $this->ownedOrder($request, $type, $id);
        if ($type === 'trip' && $order->corporateRequest) {
            return to_route('corporate.workspace', $order->corporateRequest->corporate_company_id)->with('error', 'Batalkan pengajuan dari workspace perusahaan.');
        }
        if ($order instanceof Booking) {
            app(BookingService::class)->transition($order, 'cancelled');
        } else {
            app(SouvenirCommerceService::class)->transition($order, 'cancelled');
        }

        return to_route('checkout.payment', ['type' => $type, 'id' => $id])->with('success', 'Pesanan dibatalkan. Jangan bayar kode pembayaran pesanan ini.');
    }

    private function expireOrder(Booking|SouvenirOrder $order): void
    {
        if ($order->status === 'awaiting_payment' && $order->expires_at->isPast()) {
            if ($order instanceof Booking) {
                app(BookingService::class)->transition($order, 'expired');
            } else {
                app(SouvenirCommerceService::class)->transition($order, 'expired');
            }
        }
    }

    private function ownedOrder(Request $request, string $type, int $id): Booking|SouvenirOrder
    {
        abort_unless(in_array($type, ['trip', 'souvenir'], true), 404);

        $order = ($type === 'trip' ? Booking::query() : SouvenirOrder::query())->findOrFail($id);
        $corporate = $type === 'trip' ? CorporateRequest::where('booking_id', $order->id)->first() : null;
        if ($corporate) {
            abort_unless($corporate->company->status === 'verified', 404);
            app(CorporateService::class)->membership($request->user(), $corporate->company, ['owner', 'finance']);
        } else {
            abort_unless($order->user_id === $request->user()->id, 404);
        }

        return $order;
    }
}
