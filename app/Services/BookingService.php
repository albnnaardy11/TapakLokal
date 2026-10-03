<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\Payout;
use App\Models\Promotion;
use App\Models\RewardEntry;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Facades\Cache;
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
                if ($existing->trip_id !== (int) $data['trip_id'] || $existing->participants !== (int) $data['participants'] || $existing->contact_name !== $data['contact_name'] || $existing->contact_phone !== $data['contact_phone'] || ($existing->contact_email ?? $user->email) !== ($data['contact_email'] ?? $user->email) || ($existing->traveler_details ?? []) !== ($data['traveler_details'] ?? []) || ($existing->special_request ?? '') !== ($data['special_request'] ?? '') || ($code ?? '') !== strtoupper($data['promotion_code'] ?? '')) {
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
            $vendorAmount = $trip->price * $data['participants'];
            $subtotal = $trip->selling_price * $data['participants'];
            $markup = $subtotal - $vendorAmount;
            $discount = 0;
            $promotion = null;
            if (! empty($data['promotion_code'])) {
                $promotion = Promotion::where('code', strtoupper($data['promotion_code']))->lockForUpdate()->first();
                if (! $promotion || $promotion->status !== 'published' || $promotion->starts_at->startOfDay()->isFuture() || $promotion->ends_at->endOfDay()->isPast() || $promotion->used_count >= $promotion->usage_limit || $subtotal < $promotion->minimum_amount) {
                    throw ValidationException::withMessages(['promotion_code' => 'Voucher tidak berlaku atau kuota habis.']);
                }
                $discount = $this->promotionDiscount($promotion, $subtotal, $markup);
                if ($discount < 1) {
                    throw ValidationException::withMessages(['promotion_code' => 'Voucher belum dapat digunakan untuk trip ini.']);
                }
                $promotion->increment('used_count');
            }
            $total = $subtotal - $discount;
            $fee = $markup - $discount;
            $booking = Booking::create([
                ...collect($data)->only(['participants', 'contact_name', 'contact_phone', 'idempotency_key', 'traveler_details', 'special_request'])->all(),
                'contact_email' => $data['contact_email'] ?? $user->email,
                'user_id' => $user->id, 'trip_id' => $trip->id, 'vendor_id' => $trip->vendor_id,
                'promotion_id' => $promotion?->id, 'reference' => 'TL-'.Str::upper((string) Str::ulid()),
                'subtotal' => $subtotal, 'discount' => $discount, 'total' => $total,
                'platform_fee' => $fee, 'vendor_amount' => $vendorAmount,
                'status' => 'awaiting_payment', 'expires_at' => now()->addMinutes(30),
            ]);
            $trip->increment('reserved_seats', $booking->participants);
            Payment::create(['booking_id' => $booking->id, 'reference' => $booking->reference, 'amount' => $total, 'status' => 'pending']);
            $this->audit->record('booking.created', $booking, ['total' => $total], $user->id);

            return $booking;
        });
    }

    /** @return array<int, array<string, mixed>> */
    public function availablePromotions(Booking $booking): array
    {
        return Promotion::where('status', 'published')->whereDate('starts_at', '<=', today())->whereDate('ends_at', '>=', today())
            ->whereColumn('used_count', '<', 'usage_limit')->orderBy('ends_at')->limit(30)->get()
            ->map(function (Promotion $promotion) use ($booking): array {
                $discount = $this->promotionDiscount($promotion, $booking->subtotal, $booking->subtotal - $booking->vendor_amount);

                return [
                    'code' => $promotion->code, 'name' => $promotion->name, 'type' => $promotion->type,
                    'value' => $promotion->value, 'discount' => $discount, 'minimum_amount' => $promotion->minimum_amount,
                    'maximum_discount' => $promotion->maximum_discount, 'ends_at' => $promotion->ends_at->endOfDay()->toIso8601String(),
                    'eligible' => $booking->subtotal >= $promotion->minimum_amount && $discount > 0,
                ];
            })->all();
    }

    public function updatePromotion(Booking $booking, ?string $code): void
    {
        $lock = Cache::lock('payment:checkout:'.$booking->id, 90);
        if (! $lock->get()) {
            throw ValidationException::withMessages(['promotion_code' => 'Pembayaran sedang disiapkan. Tunggu sebelum mengubah promo.']);
        }
        try {
            DB::transaction(function () use ($booking, $code): void {
                Trip::whereKey($booking->trip_id)->lockForUpdate()->firstOrFail();
                $current = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
                $payment = Payment::where('booking_id', $current->id)->lockForUpdate()->firstOrFail();
                if ($current->status !== 'awaiting_payment' || $current->expires_at->isPast() || $payment->status !== 'pending' || $payment->method || $payment->checkout_url || $payment->instructions) {
                    throw ValidationException::withMessages(['promotion_code' => 'Promo hanya dapat diubah sebelum kode pembayaran dibuat.']);
                }
                $code = $code ? strtoupper(trim($code)) : null;
                $promotions = Promotion::where(function ($query) use ($current, $code): void {
                    $query->where('code', $code)->orWhere('id', $current->promotion_id);
                })->orderBy('id')->lockForUpdate()->get();
                $promotion = $code ? $promotions->firstWhere('code', $code) : null;
                if ($code && (! $promotion || $promotion->status !== 'published' || $promotion->starts_at->startOfDay()->isFuture() || $promotion->ends_at->endOfDay()->isPast() || ($promotion->used_count >= $promotion->usage_limit && $current->promotion_id !== $promotion->id) || $current->subtotal < $promotion->minimum_amount)) {
                    throw ValidationException::withMessages(['promotion_code' => 'Promo tidak berlaku, minimum transaksi belum terpenuhi, atau kuota sudah habis.']);
                }
                $discount = $promotion ? $this->promotionDiscount($promotion, $current->subtotal, $current->subtotal - $current->vendor_amount) : 0;
                if ($promotion && $discount < 1) {
                    throw ValidationException::withMessages(['promotion_code' => 'Promo belum dapat digunakan untuk trip ini.']);
                }
                if ($current->promotion_id !== $promotion?->id) {
                    $promotions->firstWhere('id', $current->promotion_id)?->decrement('used_count');
                    $promotion?->increment('used_count');
                }
                $current->update(['promotion_id' => $promotion?->id, 'discount' => $discount, 'total' => $current->subtotal - $discount, 'platform_fee' => $current->subtotal - $current->vendor_amount - $discount]);
                $payment->update(['amount' => $current->total]);
                $this->audit->record('booking.promotion_updated', $current, ['promotion_code' => $code, 'discount' => $discount]);
            }, 3);
        } finally {
            $lock->release();
        }
    }

    private function promotionDiscount(Promotion $promotion, int $subtotal, int $markup): int
    {
        $discount = $promotion->type === 'percent' ? intdiv($subtotal * $promotion->value, 100) : $promotion->value;

        return max(0, min($discount, $promotion->maximum_discount ?? $subtotal, $markup));
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
            if ($booking->status === $target) {
                return $booking;
            }
            if ($target === 'expired' && $booking->status !== 'awaiting_payment') {
                return $booking;
            }
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
                Payment::where('booking_id', $booking->id)->where('status', 'pending')->update(['status' => $target]);
                $trip->decrement('reserved_seats', $booking->participants);
                if ($booking->promotion_id) {
                    Promotion::whereKey($booking->promotion_id)->decrement('used_count');
                }
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
