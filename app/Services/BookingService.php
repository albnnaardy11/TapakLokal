<?php
namespace App\Services;

use App\Models\{Booking, Payment, Payout, Promotion, RewardEntry, Trip, User, Vendor};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BookingService
{
    public function __construct(private AuditService $audit) {}

    /** @param array<string, mixed> $data */
    public function create(User $user, array $data): Booking
    {
        return DB::transaction(function () use ($user, $data) {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $existing = Booking::where('user_id', $user->id)->where('idempotency_key', $data['idempotency_key'])->first();
            if ($existing) {
                $code = $existing->promotion_id ? $existing->promotion()->value('code') : null;
                if ($existing->trip_id !== (int) $data['trip_id'] || $existing->participants !== (int) $data['participants'] || $existing->contact_name !== $data['contact_name'] || $existing->contact_phone !== $data['contact_phone'] || ($code ?? '') !== strtoupper($data['promotion_code'] ?? '')) {
                    throw ValidationException::withMessages(['idempotency_key' => 'Kunci pemesanan sudah digunakan untuk pesanan berbeda.']);
                }
                return $existing;
            }
            $trip = Trip::lockForUpdate()->findOrFail($data['trip_id']);
            $vendor = Vendor::whereKey($trip->vendor_id)->lockForUpdate()->firstOrFail();
            if ($trip->status !== 'published' || $vendor->status !== 'verified' || $trip->departure_date->isPast() && ! $trip->departure_date->isToday()) {
                throw ValidationException::withMessages(['trip_id' => 'Trip tidak tersedia untuk dipesan.']);
            }
            if ($trip->capacity - $trip->reserved_seats < $data['participants']) {
                throw ValidationException::withMessages(['participants' => 'Kuota perjalanan tidak mencukupi.']);
            }
            $subtotal = $trip->price * $data['participants'];
            $discount = 0;
            $promotion = null;
            if (! empty($data['promotion_code'])) {
                $promotion = Promotion::where('code', strtoupper($data['promotion_code']))->lockForUpdate()->first();
                if (! $promotion || $promotion->status !== 'published' || $promotion->starts_at->startOfDay()->isFuture() || $promotion->ends_at->endOfDay()->isPast() || $promotion->used_count >= $promotion->usage_limit || $subtotal < $promotion->minimum_amount) {
                    throw ValidationException::withMessages(['promotion_code' => 'Voucher tidak berlaku atau kuota habis.']);
                }
                $discount = $promotion->type === 'percent' ? intdiv($subtotal * $promotion->value, 100) : $promotion->value;
                $discount = min($discount, $promotion->maximum_discount ?? $subtotal, $subtotal - 1);
                $promotion->increment('used_count');
            }
            $total = $subtotal - $discount;
            $fee = intdiv($total * max(0, min(10000, (int) config('platform.commission_bps'))), 10000);
            $booking = Booking::create([
                ...collect($data)->only(['participants', 'contact_name', 'contact_phone', 'idempotency_key'])->all(),
                'user_id' => $user->id, 'trip_id' => $trip->id, 'vendor_id' => $trip->vendor_id,
                'promotion_id' => $promotion?->id, 'reference' => 'TL-'.Str::upper((string) Str::ulid()),
                'subtotal' => $subtotal, 'discount' => $discount, 'total' => $total,
                'platform_fee' => $fee, 'vendor_amount' => $total - $fee,
                'status' => 'awaiting_payment', 'expires_at' => now()->addMinutes(30),
            ]);
            $trip->increment('reserved_seats', $booking->participants);
            Payment::create(['booking_id' => $booking->id, 'reference' => $booking->reference, 'amount' => $total, 'status' => 'pending']);
            $this->audit->record('booking.created', $booking, ['total' => $total], $user->id);
            return $booking;
        });
    }

    public function transition(Booking $booking, string $target): Booking
    {
        return DB::transaction(function () use ($booking, $target) {
            $trip = Trip::whereKey($booking->trip_id)->lockForUpdate()->firstOrFail();
            $booking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
            $allowed = [
                'awaiting_payment' => ['cancelled', 'expired'],
                'paid' => ['confirmed'],
                'confirmed' => ['ongoing'],
                'ongoing' => ['completed'],
            ];
            if ($booking->status === $target) { return $booking; }
            if ($target === 'expired' && $booking->expires_at->isFuture()) {
                throw ValidationException::withMessages(['status' => 'Batas waktu pembayaran belum berakhir.']);
            }
            if (! in_array($target, $allowed[$booking->status] ?? [], true)) {
                throw ValidationException::withMessages(['status' => 'Perubahan status pemesanan tidak diizinkan.']);
            }
            if ($target === 'ongoing' && $trip->departure_date->isFuture()) {
                throw ValidationException::withMessages(['status' => 'Perjalanan belum memasuki tanggal keberangkatan.']);
            }
            if ($target === 'completed' && $trip->end_date->isFuture()) {
                throw ValidationException::withMessages(['status' => 'Tanggal selesai perjalanan belum tercapai.']);
            }
            $before = $booking->status;
            $booking->update(['status' => $target]);
            if (in_array($target, ['cancelled', 'expired'], true)) {
                $trip->decrement('reserved_seats', $booking->participants);
                if ($booking->promotion_id) { Promotion::whereKey($booking->promotion_id)->decrement('used_count'); }
            }
            if ($target === 'completed') {
                Payout::firstOrCreate(['booking_id' => $booking->id], ['vendor_id' => $booking->vendor_id, 'amount' => $booking->vendor_amount, 'status' => 'eligible']);
                RewardEntry::firstOrCreate(['reference' => 'booking:'.$booking->id], ['user_id' => $booking->user_id, 'booking_id' => $booking->id, 'points' => intdiv($booking->total, 10000), 'description' => 'Perjalanan selesai '.$booking->reference]);
            }
            $this->audit->record('booking.'.$target, $booking, ['before' => $before, 'after' => $target]);
            return $booking;
        });
    }
}
