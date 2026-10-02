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
use App\Services\CorporateService;
use App\Services\PaymentInstructionService;
use App\Services\PaymentMethodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

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
        $order->load($type === 'trip' ? ['trip', 'vendor:id,name', 'payment'] : ['items', 'vendor:id,name', 'payment']);

        return Inertia::render('CheckoutPayment', [
            'corporateWorkspaceUrl' => $type === 'trip' && ($corporate = CorporateRequest::where('booking_id', $order->id)->first()) ? route('corporate.workspace', $corporate->corporate_company_id) : null, 'kind' => $type, 'order' => $order, 'paymentMethods' => $methods->catalog(), 'paymentPreferences' => $methods->preferences($request->user()),
            'gatewayReady' => filled(config('platform.midtrans_server_key')), 'serverNow' => now()->toIso8601String(),
        ]);
    }

    public function charge(Request $request, string $type, int $id, PaymentInstructionService $instructions): RedirectResponse
    {
        $order = $this->ownedOrder($request, $type, $id);
        $data = $request->validate(['method' => ['required', 'string', 'max:30']]);
        $instructions->start($order, $data['method']);

        return to_route('checkout.payment', ['type' => $type, 'id' => $id]);
    }

    public function check(Request $request, string $type, int $id): RedirectResponse
    {
        $order = $this->ownedOrder($request, $type, $id);
        if (! config('platform.midtrans_server_key')) {
            return back()->with('error', 'Pembayaran online belum aktif.');
        }
        if ($type === 'trip') {
            ReconcilePayment::dispatch($order->reference);
        } else {
            ReconcileSouvenirPayment::dispatch($order->reference);
        }

        return back()->with('success', 'Pemeriksaan pembayaran diminta. Status diperbarui setelah verifikasi penyedia.');
    }

    public function preferences(Request $request, PaymentMethodService $methods): RedirectResponse
    {
        $data = $request->validate(['saved' => ['present', 'array', 'max:6'], 'saved.*' => ['required', 'string', 'distinct', 'max:30'], 'primary' => ['nullable', 'string', 'max:30']]);
        $methods->save($request->user(), $data['saved'], $data['primary'] ?? null);

        return back()->with('success', 'Preferensi pembayaran tersimpan.');
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
