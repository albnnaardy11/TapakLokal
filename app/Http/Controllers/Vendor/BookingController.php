<?php
namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use Illuminate\Http\{RedirectResponse, Request};

class BookingController extends Controller
{
    public function update(Request $request, Booking $booking, BookingService $service): RedirectResponse
    {
        abort_unless($booking->vendor->user_id === $request->user()->id, 404);
        abort_unless($booking->vendor->status === 'verified', 403);
        $data = $request->validate(['status' => ['required', \Illuminate\Validation\Rule::in(['confirmed', 'ongoing', 'completed'])]]);
        $service->transition($booking, $data['status']);
        return back()->with('success', 'Status pemesanan diperbarui.');
    }
}

