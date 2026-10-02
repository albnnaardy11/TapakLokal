<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CorporateCompany;
use App\Models\CorporateMembership;
use App\Models\CorporateRequest;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CorporateService
{
    public function __construct(private AuditService $audit, private BookingService $bookings) {}

    public function authorizeBooking(User $user, Booking $booking): void
    {
        $corporate = $booking->corporateRequest;
        if ($corporate) {
            abort_unless($corporate->company->status === 'verified', 404);
            $this->membership($user, $corporate->company, ['owner', 'finance']);

            return;
        }
        abort_unless($booking->user_id === $user->id, 404);
    }

    /** @param list<string> $roles */
    public function membership(User $user, CorporateCompany $company, array $roles = []): CorporateMembership
    {
        $member = $company->memberships()->where('user_id', $user->id)->where('status', 'active')->firstOrFail();
        abort_if($roles && ! in_array($member->role, $roles, true), 403);

        return $member;
    }

    public function offer(CorporateRequest $request, int $tripId, string $terms): void
    {
        DB::transaction(function () use ($request, $tripId, $terms): void {
            $company = CorporateCompany::whereKey($request->corporate_company_id)->lockForUpdate()->firstOrFail();
            $request = CorporateRequest::whereKey($request->id)->lockForUpdate()->firstOrFail();
            abort_unless($company->status === 'verified', 422, 'Perusahaan belum aktif.');
            if (! in_array($request->status, ['submitted', 'quoted', 'approved'], true)) {
                throw ValidationException::withMessages(['status' => 'Penawaran tidak dapat diubah setelah diputuskan.']);
            }
            $trip = Trip::whereKey($tripId)->lockForUpdate()->firstOrFail();
            $vendor = Vendor::whereKey($trip->vendor_id)->lockForUpdate()->firstOrFail();
            if ($trip->status !== 'published' || $trip->type !== 'private-trip' || $vendor->status !== 'verified' || $trip->departure_date->lt(today()) || ! $trip->departure_date->equalTo($request->departure_date) || ! $trip->end_date->equalTo($request->end_date) || $trip->capacity - $trip->reserved_seats < $request->participants) {
                throw ValidationException::withMessages(['trip_id' => 'Pilih private trip terbit, vendor terverifikasi, tanggal sesuai, dan kuota mencukupi.']);
            }
            $quote = ['version' => count($request->quote_history ?? []) + 1, 'trip_id' => $trip->id, 'title' => $trip->title, 'destination' => $trip->destination, 'vendor' => $vendor->name, 'vendor_id' => $vendor->id, 'unit_price' => $trip->selling_price, 'vendor_price' => $trip->price, 'participants' => $request->participants, 'total' => $trip->selling_price * $request->participants, 'itinerary' => $trip->itinerary, 'meeting_point' => $trip->meeting_point, 'terms' => $terms, 'issued_at' => now()->toIso8601String()];
            $request->update(['trip_id' => $trip->id, 'quote' => $quote, 'quote_total' => $quote['total'], 'quote_history' => [...($request->quote_history ?? []), $quote], 'quote_expires_at' => now()->addDays(3)->min($trip->departure_date->copy()->endOfDay()), 'status' => 'quoted', 'approved_by' => null, 'approved_at' => null, 'decision_note' => null]);
            $this->notify($request, 'Penawaran perjalanan tersedia');
            $this->audit->record('corporate.quote.issued', $request, ['version' => $quote['version'], 'total' => $quote['total']]);
        }, 3);
    }

    public function decide(User $user, CorporateRequest $request, string $decision, string $note): void
    {
        DB::transaction(function () use ($user, $request, $decision, $note): void {
            $company = CorporateCompany::whereKey($request->corporate_company_id)->lockForUpdate()->firstOrFail();
            $this->membership($user, $company, ['owner', 'approver']);
            $request = CorporateRequest::whereKey($request->id)->lockForUpdate()->firstOrFail();
            if ($request->user_id === $user->id) {
                throw ValidationException::withMessages(['decision' => 'Pengaju tidak boleh menyetujui pengajuannya sendiri. Undang approver lain.']);
            }
            if ($company->status !== 'verified' || $request->status !== 'quoted' || $request->quote_expires_at->isPast()) {
                throw ValidationException::withMessages(['decision' => 'Penawaran tidak aktif. Minta admin menyiapkan penawaran baru.']);
            }
            if ($decision === 'approved') {
                if ($request->quote_total > $request->budget) {
                    throw ValidationException::withMessages(['decision' => 'Penawaran melebihi anggaran pengajuan. Ajukan kebutuhan baru dengan anggaran yang disetujui.']);
                }
                $this->checkBudget($company, $request);
            }
            $request->update(['status' => $decision, 'approved_by' => $user->id, 'approved_at' => now(), 'decision_note' => $note]);
            $this->notify($request, $decision === 'approved' ? 'Penawaran disetujui' : 'Penawaran ditolak');
            $this->audit->record('corporate.request.'.$decision, $request);
        }, 3);
    }

    public function book(User $user, CorporateRequest $request): int
    {
        return DB::transaction(function () use ($user, $request): int {
            $company = CorporateCompany::whereKey($request->corporate_company_id)->lockForUpdate()->firstOrFail();
            $this->membership($user, $company, ['owner', 'finance']);
            $request = CorporateRequest::whereKey($request->id)->lockForUpdate()->firstOrFail();
            abort_unless($company->status === 'verified', 403);
            if ($request->booking_id) {
                return $request->booking_id;
            }
            if ($company->status !== 'verified' || $request->status !== 'approved' || $request->quote_expires_at->isPast()) {
                throw ValidationException::withMessages(['status' => 'Pengajuan harus disetujui dan penawaran masih berlaku.']);
            }
            $trip = Trip::whereKey($request->trip_id)->lockForUpdate()->firstOrFail();
            if ($trip->title !== $request->quote['title'] || $trip->destination !== $request->quote['destination'] || $trip->itinerary !== $request->quote['itinerary'] || $trip->meeting_point !== $request->quote['meeting_point'] || $trip->type !== 'private-trip' || $trip->vendor_id !== $request->quote['vendor_id'] || $trip->price !== $request->quote['vendor_price'] || $trip->selling_price !== $request->quote['unit_price'] || ! $trip->departure_date->equalTo($request->departure_date) || ! $trip->end_date->equalTo($request->end_date)) {
                throw ValidationException::withMessages(['status' => 'Harga atau tanggal berubah. Minta penawaran baru sebelum pembayaran.']);
            }
            if (count($request->travelers ?? []) !== $request->participants) {
                throw ValidationException::withMessages(['travelers' => 'Lengkapi daftar peserta sebelum pemesanan.']);
            }
            $this->checkBudget($company, $request);
            $booking = $this->bookings->create($user, ['trip_id' => $trip->id, 'participants' => $request->participants, 'contact_name' => $company->pic_name, 'contact_phone' => $company->phone, 'contact_email' => $company->work_email, 'idempotency_key' => (string) Str::uuid(), 'special_request' => $request->needs, 'traveler_details' => $request->travelers]);
            $request->update(['booking_id' => $booking->id, 'status' => 'booked']);
            $this->notify($request, 'Pesanan corporate siap dibayar');
            $this->audit->record('corporate.request.booked', $request, ['booking_id' => $booking->id]);

            return $booking->id;
        }, 3);
    }

    private function notify(CorporateRequest $request, string $title): void
    {
        $request->company->memberships()->where('status', 'active')->where(fn ($scope) => $scope->whereIn('role', ['owner', 'finance', 'approver'])->orWhere('user_id', $request->user_id))->with('user')->chunkById(100, function ($members) use ($request, $title): void {
            foreach ($members as $member) {
                $member->user->notifications()->create(['id' => (string) Str::uuid7(), 'type' => 'corporate.status', 'data' => ['title' => $title, 'reference' => $request->reference, 'url' => route('corporate.workspace', $request->corporate_company_id, false)]]);
            }
        });
    }

    private function checkBudget(CorporateCompany $company, CorporateRequest $request): void
    {
        if (! $company->monthly_limit) {
            return;
        }
        $used = $company->requests()->whereKeyNot($request->id)->whereBetween('departure_date', [$request->departure_date->copy()->startOfMonth(), $request->departure_date->copy()->endOfMonth()])
            ->where(function ($query): void {
                $query->where(fn ($approved) => $approved->where('status', 'approved')->where('quote_expires_at', '>', now()))->orWhere(function ($booked): void {
                    $booked->where('status', 'booked')->whereHas('booking', fn ($booking) => $booking->whereNotIn('status', ['cancelled', 'expired', 'refunded']));
                });
            })->sum('quote_total');
        if ($used + $request->quote_total > $company->monthly_limit) {
            throw ValidationException::withMessages(['budget' => 'Batas anggaran perusahaan untuk bulan keberangkatan terlampaui.']);
        }
    }
}
