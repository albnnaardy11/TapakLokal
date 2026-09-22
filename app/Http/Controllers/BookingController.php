<?php
namespace App\Http\Controllers;

use App\Http\Requests\BookingRequest;
use App\Models\{Booking, Refund, Trip};
use App\Services\{AuditService, BookingService};
use Illuminate\Http\{RedirectResponse, Request};
use Illuminate\Support\Facades\DB;
use Inertia\{Inertia, Response};

class BookingController extends Controller
{
    public function catalog(Request $request): Response
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:100'], 'type' => ['nullable', \Illuminate\Validation\Rule::in(['open-trip', 'private-trip'])], 'date' => ['nullable', 'date_format:Y-m-d']]);
        $trips = Trip::with('vendor:id,name')->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))
            ->where('departure_date', '>=', today()->toDateString())
            ->when($filters['q'] ?? null, fn ($query, $term) => $query->where(fn ($search) => $search->where('title', 'like', '%'.$term.'%')->orWhere('destination', 'like', '%'.$term.'%')))
            ->when($filters['type'] ?? null, fn ($query, $type) => $query->where('type', $type))
            ->when($filters['date'] ?? null, fn ($query, $date) => $query->where('departure_date', $date))
            ->orderBy('departure_date')->orderBy('id')->paginate(12, ['id', 'vendor_id', 'title', 'slug', 'type', 'destination', 'image_url', 'departure_date', 'capacity', 'reserved_seats', 'price'])->withQueryString();
        return Inertia::render('Catalog', ['trips' => $trips, 'filters' => $filters]);
    }

    public function detail(string $tripType, string $trip): Response
    {
        $record = Trip::with('vendor:id,name')->where('type', $tripType)->where('slug', $trip)->where('status', 'published')->whereHas('vendor', fn ($query) => $query->where('status', 'verified'))->firstOrFail();
        return Inertia::render('TripDetail', [
            'tripType' => $tripType, 'trip' => $trip, 'tripData' => $record,
            'bookingKey' => (string) \Illuminate\Support\Str::uuid(),
            'reviews' => \App\Models\Review::with('user:id,name')->where('trip_id', $record->id)->where('status', 'published')->latest('id')->limit(10)->get(['id', 'user_id', 'rating', 'body', 'vendor_response', 'created_at']),
        ]);
    }

    public function store(BookingRequest $request, BookingService $service): RedirectResponse
    {
        return to_route('bookings.show', $service->create($request->user(), $request->validated()));
    }

    public function show(Request $request, Booking $booking): Response
    {
        abort_unless($booking->user_id === $request->user()->id, 404);
        return Inertia::render('Booking', ['booking' => $booking->load(['trip', 'vendor:id,name,phone', 'payment', 'refund']), 'gatewayReady' => (bool) config('platform.midtrans_server_key')]);
    }

    public function cancel(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 404);
        $service->transition($booking, 'cancelled');
        return back()->with('success', 'Pesanan dibatalkan.');
    }

    public function refund(Request $request, Booking $booking, AuditService $audit): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 404);
        $data = $request->validate(['reason' => ['required', 'string', 'min:10', 'max:2000']]);
        DB::transaction(function () use ($booking, $data, $request, $audit) {
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            abort_unless(in_array($booking->status, ['paid', 'confirmed', 'ongoing', 'completed']) && $booking->payment?->status === 'paid', 422);
            abort_if($booking->payout && in_array($booking->payout->status, ['pending', 'processing', 'paid']), 422, 'Payout sedang diproses. Hubungi dukungan untuk sengketa.');
            $refund = Refund::firstOrCreate(['booking_id' => $booking->id], ['user_id' => $request->user()->id, 'amount' => $booking->total, 'reason' => $data['reason'], 'status' => 'pending']);
            if ($refund->wasRecentlyCreated) { $audit->record('refund.requested', $refund); }
        });
        return back()->with('success', 'Permintaan refund tercatat untuk ditinjau.');
    }
}
