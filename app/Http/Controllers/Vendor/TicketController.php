<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TicketController extends Controller
{
    private function authorizeTicket(Request $request, Booking $booking): void
    {
        abort_unless($booking->vendor->user_id === $request->user()->id, 404);
        abort_unless($booking->vendor->status === 'verified', 403);
        abort_unless(in_array($booking->status, ['paid', 'confirmed', 'ongoing'], true) && $booking->payment?->status === 'paid', 422, 'Tiket tidak aktif atau pembayaran belum lunas.');
    }

    public function show(Request $request, Booking $booking): Response
    {
        $this->authorizeTicket($request, $booking);

        return Inertia::render('Vendor/Ticket', ['booking' => $booking->only(['id', 'reference', 'contact_name', 'participants', 'traveler_details', 'checked_in_at']), 'trip' => $booking->trip->only(['title', 'departure_date', 'end_date']), 'checkInUrl' => $request->fullUrl()]);
    }

    public function checkIn(Request $request, Booking $booking): RedirectResponse
    {
        DB::transaction(function () use ($request, $booking): void {
            $lockedBooking = Booking::lockForUpdate()->findOrFail($booking->id);
            $this->authorizeTicket($request, $lockedBooking);
            if (! $lockedBooking->checked_in_at) {
                $lockedBooking->checked_in_at = now();
                $lockedBooking->save();
            }
        });

        return back()->with('success', 'Check-in berhasil.');
    }
}
