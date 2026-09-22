<?php
namespace App\Services;

use App\Models\{Booking, LedgerEntry, Payment, Payout, Refund, Trip};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Validation\ValidationException;

class FinanceService
{
    public function __construct(private AuditService $audit) {}

    public function apiBase(): string
    {
        return config('platform.midtrans_production') ? 'https://api.midtrans.com' : 'https://api.sandbox.midtrans.com';
    }

    public function checkout(Booking $booking): string
    {
        if (! config('platform.midtrans_server_key')) {
            throw ValidationException::withMessages(['payment' => 'Gateway pembayaran belum diaktifkan. Pesanan tersimpan; hubungi dukungan.']);
        }
        $lock = Cache::lock('payment:checkout:'.$booking->id, 45);
        if (! $lock->get()) {
            throw ValidationException::withMessages(['payment' => 'Pembayaran sedang disiapkan. Coba lagi beberapa saat.']);
        }
        try {
            $booking = $booking->fresh();
            $payment = Payment::where('booking_id', $booking->id)->firstOrFail();
            if ($booking->status !== 'awaiting_payment' || $booking->expires_at->isPast()) {
                throw ValidationException::withMessages(['payment' => 'Pesanan tidak dapat dibayar.']);
            }
            if ($payment->checkout_url) { return $payment->checkout_url; }
            $base = config('platform.midtrans_production') ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
            try {
                $response = Http::withBasicAuth(config('platform.midtrans_server_key'), '')->acceptJson()->connectTimeout(5)->timeout(15)->post($base.'/snap/v1/transactions', [
                'transaction_details' => ['order_id' => $payment->reference, 'gross_amount' => $payment->amount],
                'customer_details' => ['first_name' => $booking->contact_name, 'phone' => $booking->contact_phone],
                'expiry' => ['start_time' => $booking->created_at->format('Y-m-d H:i:s O'), 'unit' => 'minutes', 'duration' => 30],
                'callbacks' => ['finish' => route('bookings.show', $booking)],
                ]);
            } catch (ConnectionException $exception) {
                report($exception);
                throw ValidationException::withMessages(['payment' => 'Gateway belum merespons. Gunakan pesanan yang sama saat mencoba kembali.']);
            }
            if (! $response->successful() || ! is_string($response->json('redirect_url'))) {
                throw ValidationException::withMessages(['payment' => 'Gateway belum memberikan tautan pembayaran. Hubungi dukungan dengan nomor pesanan; jangan membuat pembayaran ganda.']);
            }
            $url = $response->json('redirect_url');
            if (parse_url($url, PHP_URL_SCHEME) !== 'https' || ! in_array(parse_url($url, PHP_URL_HOST), ['app.midtrans.com', 'app.sandbox.midtrans.com'], true)) {
                throw ValidationException::withMessages(['payment' => 'Respons gateway tidak valid.']);
            }
            $eligible = DB::transaction(function () use ($booking, $payment, $url): bool {
                $current = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
                Payment::whereKey($payment->id)->update(['checkout_url' => $url]);
                return $current->status === 'awaiting_payment' && $current->expires_at->isFuture();
            });
            if (! $eligible) {
                throw ValidationException::withMessages(['payment' => 'Status pesanan sudah berubah. Muat ulang detail pesanan.']);
            }
            return $url;
        } finally {
            $lock->release();
        }
    }

    /** @param array<string, mixed> $data */
    public function applyStatus(array $data): void
    {
        $initial = Payment::where('reference', $data['order_id'])->firstOrFail();
        $original = Booking::findOrFail($initial->booking_id);
        DB::transaction(function () use ($data, $initial, $original) {
            $trip = Trip::whereKey($original->trip_id)->lockForUpdate()->firstOrFail();
            $booking = Booking::whereKey($original->id)->lockForUpdate()->firstOrFail();
            $payment = Payment::whereKey($initial->id)->lockForUpdate()->firstOrFail();
            if (! preg_match('/^([0-9]+)(?:\\.0{1,2})?$/', (string) ($data['gross_amount'] ?? ''), $amount) || (int) $amount[1] !== $payment->amount) {
                throw ValidationException::withMessages(['amount' => 'Nominal gateway berbeda dari pesanan.']);
            }
            $status = $data['transaction_status'];
            $settled = $status === 'settlement' || ($status === 'capture' && ($data['fraud_status'] ?? '') === 'accept');
            if ($settled && ! in_array($payment->status, ['paid', 'refunded', 'reconciliation_required'], true)) {
                $late = $booking->status !== 'awaiting_payment' || $booking->expires_at->isPast();
                if ($late && $booking->status === 'awaiting_payment') {
                    $booking->update(['status' => 'expired']);
                    $trip->decrement('reserved_seats', $booking->participants);
                    if ($booking->promotion_id) {
                        \App\Models\Promotion::whereKey($booking->promotion_id)->decrement('used_count');
                    }
                }
                $payment->update(['status' => $late ? 'reconciliation_required' : 'paid', 'paid_at' => now(), 'provider_reference' => $data['transaction_id']]);
                if (! $late) { $booking->update(['status' => 'paid']); }
                $this->entry($booking, 'receipt:cash', 'gateway_cash', $booking->total, 'Pembayaran diterima gateway');
                $this->entry($booking, 'receipt:escrow', 'customer_liability', -$booking->total, 'Dana menunggu penyelesaian layanan');
                $this->audit->record('payment.'.$payment->status, $payment, ['amount' => $payment->amount]);
            }
            if ($status === 'refund' && $payment->status === 'paid') {
                $payout = Payout::where('booking_id', $booking->id)->lockForUpdate()->first();
                if ($payout && in_array($payout->status, ['processing', 'paid'], true)) {
                    $payment->update(['status' => 'reconciliation_required']);
                    $this->audit->record('payment.refund_reconciliation_required', $payment);
                    return;
                }
                $payment->update(['status' => 'refunded']);
                $wasReserved = ! in_array($booking->status, ['cancelled', 'expired', 'refunded'], true);
                $booking->update(['status' => 'refunded']);
                if ($wasReserved) { $trip->decrement('reserved_seats', $booking->participants); }
                Refund::updateOrCreate(['booking_id' => $booking->id], ['user_id' => $booking->user_id, 'amount' => $booking->total, 'reason' => 'Refund terkonfirmasi gateway', 'status' => 'paid']);
                if ($payout) { $payout->update(['status' => 'reversed']); }
                $this->entry($booking, 'refund:cash', 'gateway_cash', -$booking->total, 'Refund terkonfirmasi');
                $this->entry($booking, 'refund:escrow', 'customer_liability', $booking->total, 'Kewajiban dikembalikan');
                $earned = \App\Models\RewardEntry::where('reference', 'booking:'.$booking->id)->first();
                if ($earned) {
                    \App\Models\RewardEntry::firstOrCreate(['reference' => 'refund:'.$booking->id], ['user_id' => $booking->user_id, 'booking_id' => $booking->id, 'points' => -$earned->points, 'description' => 'Pembalikan poin refund']);
                }
                $this->audit->record('payment.refunded', $payment);
            }
            if ($status === 'partial_refund' && $payment->status === 'paid') {
                $payment->update(['status' => 'reconciliation_required']);
                $this->audit->record('payment.partial_refund_requires_reconciliation', $payment);
            }
            if (in_array($status, ['deny', 'cancel', 'expire'], true) && $payment->status === 'pending') {
                $payment->update(['status' => $status === 'expire' ? 'expired' : 'failed']);
                if ($booking->status === 'awaiting_payment') {
                    $booking->update(['status' => 'expired']);
                    $trip->decrement('reserved_seats', $booking->participants);
                    if ($booking->promotion_id) { \App\Models\Promotion::whereKey($booking->promotion_id)->decrement('used_count'); }
                }
            }
        });
    }

    private function entry(Booking $booking, string $suffix, string $account, int $amount, string $description): void
    {
        LedgerEntry::firstOrCreate(['reference' => $booking->reference.':'.$suffix], ['booking_id' => $booking->id, 'account' => $account, 'amount' => $amount, 'description' => $description]);
    }
}
