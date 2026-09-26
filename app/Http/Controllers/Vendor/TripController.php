<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\TripRequest;
use App\Models\Trip;
use App\Models\Vendor;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class TripController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Vendor/TripForm', ['trip' => null]);
    }

    public function edit(Request $request, Trip $trip): Response
    {
        abort_unless($trip->vendor->user_id === $request->user()->id, 404);

        return Inertia::render('Vendor/TripForm', ['trip' => $trip]);
    }

    public function store(TripRequest $request, AuditService $audit): RedirectResponse
    {
        $vendor = Vendor::where('user_id', $request->user()->id)->firstOrFail();
        abort_unless($vendor->status === 'verified', 403, 'Vendor harus terverifikasi.');
        DB::transaction(function () use ($request, $vendor, $audit) {
            $vendor = Vendor::whereKey($vendor->id)->lockForUpdate()->firstOrFail();
            abort_unless($vendor->status === 'verified', 403);
            $trip = Trip::create([...$request->validated(), 'vendor_id' => $vendor->id]);
            $audit->record('trip.created', $trip);
        });

        return to_route('vendor.section', 'trips')->with('success', 'Trip tersimpan.');
    }

    public function update(TripRequest $request, Trip $trip, AuditService $audit): RedirectResponse
    {
        abort_unless($trip->vendor->user_id === $request->user()->id, 404);
        abort_unless($trip->vendor->status === 'verified', 403);
        DB::transaction(function () use ($request, $trip, $audit) {
            $trip = Trip::whereKey($trip->id)->lockForUpdate()->firstOrFail();
            $vendor = Vendor::whereKey($trip->vendor_id)->lockForUpdate()->firstOrFail();
            abort_unless($vendor->status === 'verified', 403);
            if ($trip->bookings()->exists()) {
                throw ValidationException::withMessages(['title' => 'Trip yang sudah memiliki pesanan tidak dapat diubah. Buat jadwal baru atau hubungi operasional.']);
            }
            $trip->update($request->validated());
            $audit->record('trip.updated', $trip);
        });

        return to_route('vendor.section', 'trips')->with('success', 'Trip diperbarui dan memerlukan moderasi sebelum terbit.');
    }

    public function destroy(Request $request, Trip $trip, AuditService $audit): RedirectResponse
    {
        abort_unless($trip->vendor->user_id === $request->user()->id, 404);
        DB::transaction(function () use ($trip, $audit) {
            $trip = Trip::whereKey($trip->id)->lockForUpdate()->firstOrFail();
            if ($trip->bookings()->exists()) {
                throw ValidationException::withMessages(['delete' => 'Trip yang memiliki pesanan tidak dapat dihapus. Hubungi operasional untuk mengarsipkan jadwal.']);
            }
            $audit->record('trip.deleted', $trip, ['title' => $trip->title]);
            $trip->delete();
        });

        return to_route('vendor.section', 'trips')->with('success', 'Trip dihapus dari katalog.');
    }
}
