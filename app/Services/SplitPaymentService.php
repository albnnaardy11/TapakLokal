<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\LedgerEntry;
use App\Models\Payment;
use App\Models\PaymentShare;
use App\Models\Refund;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SplitPaymentService
{
    public function create(Booking $booking, string $method): void
    {
        if (config('platform.midtrans_production') || ! config('platform.midtrans_server_key')) {
            throw ValidationException::withMessages(['payment' => 'Split bill saat ini tersedia untuk pengujian sandbox.']);
        }
        app(PaymentMethodService::class)->requireEnabled($method);
        $lock = Cache::lock('payment:checkout:'.$booking->id, 90);
        if (! $lock->get()) {
            throw ValidationException::withMessages(['payment' => 'Pembayaran sedang disiapkan. Coba lagi.']);
        }
        try {
            DB::transaction(function () use ($booking, $method): void {
                $current = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();
                $payment = Payment::where('booking_id', $current->id)->lockForUpdate()->firstOrFail();
                $shares = PaymentShare::where('booking_id', $current->id)->get();
                if ($shares->isNotEmpty()) {
                    if ($payment->method !== $method) {
                        throw ValidationException::withMessages(['payment' => 'Semua anggota menggunakan metode yang sudah dipilih.']);
                    }

                    return;
                }
                if ($current->corporateRequest()->exists() || $current->participants < 2 || count($current->traveler_details ?? []) !== $current->participants || $current->status !== 'awaiting_payment' || $current->expires_at->isPast() || $payment->status !== 'pending' || $payment->method || $payment->instructions || $payment->checkout_url) {
                    throw ValidationException::withMessages(['payment' => 'Split bill memerlukan minimal dua peserta lengkap dan belum ada pembayaran yang dibuat.']);
                }
                $base = intdiv($current->total, $current->participants);
                if ($base < 1) {
                    throw ValidationException::withMessages(['payment' => 'Nominal per anggota terlalu kecil.']);
                }
                foreach ($current->traveler_details as $index => $traveler) {
                    PaymentShare::create(['booking_id' => $current->id, 'reference' => $current->reference.'-S'.($index + 1), 'position' => $index + 1, 'token' => Str::random(64), 'label' => $traveler['name'], 'amount' => $base + ($index < $current->total % $current->participants ? 1 : 0), 'method' => $method, 'status' => 'pending']);
                }
                $payment->update(['method' => $method, 'instructions' => ['split_bill' => true], 'reconciled_at' => now()]);
                app(AuditService::class)->record('payment.split_created', $payment, ['parts' => $current->participants]);
            });
        } finally {
            $lock->release();
        }
    }

    public function charge(PaymentShare $share): void
    {
        if (config('platform.midtrans_production') || ! config('platform.midtrans_server_key')) {
            throw ValidationException::withMessages(['payment' => 'Split bill saat ini tersedia untuk pengujian sandbox.']);
        }
        $lock = Cache::lock('payment:checkout:'.$share->booking_id, 90);
        if (! $lock->get()) {
            throw ValidationException::withMessages(['payment' => 'Instruksi sedang disiapkan. Coba lagi.']);
        }
        try {
            $share = $share->fresh();
            $booking = $share->booking;
            if ($share->status !== 'pending' || $booking->status !== 'awaiting_payment' || $booking->expires_at->isPast()) {
                throw ValidationException::withMessages(['payment' => 'Tagihan sudah tidak dapat dibayar.']);
            }
            if ($share->instructions) {
                return;
            }
            app(PaymentMethodService::class)->requireEnabled($share->method);
            $client = Http::withBasicAuth(config('platform.midtrans_server_key'), '')->acceptJson()->connectTimeout(2)->timeout(6);
            $base = app(FinanceService::class)->apiBase();
            $response = $client->get($base.'/v2/'.rawurlencode($share->reference).'/status');
            if ($response->status() === 404 || (string) $response->json('status_code') === '404') {
                $response = $client->post($base.'/v2/charge', [
                    'transaction_details' => ['order_id' => $share->reference, 'gross_amount' => $share->amount],
                    'custom_expiry' => ['order_time' => $booking->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s O'), 'expiry_duration' => 30, 'unit' => 'minute'],
                    ...match ($share->method) {
                        'bca' => ['payment_type' => 'bank_transfer', 'bank_transfer' => ['bank' => 'bca']],
                        'mandiri' => ['payment_type' => 'echannel', 'echannel' => ['bill_info1' => 'Pesanan:', 'bill_info2' => 'TapakLokal']],
                        'alfamart', 'indomaret' => ['payment_type' => 'cstore', 'cstore' => ['store' => $share->method, 'message' => 'Pesanan TapakLokal']],
                        'ovo' => ['payment_type' => 'qris', 'qris' => ['acquirer' => 'gopay']],
                        'gopay' => ['payment_type' => 'gopay', 'gopay' => ['enable_callback' => true, 'callback_url' => route('split.pay', $share->token)]],
                    },
                ]);
                if ((string) $response->json('status_code') === '406') {
                    $response = $client->get($base.'/v2/'.rawurlencode($share->reference).'/status');
                }
            }
            if (! $response->successful() || ! in_array((string) $response->json('status_code'), ['200', '201'], true) || ! is_array($response->json())) {
                throw ValidationException::withMessages(['payment' => 'Midtrans belum memberikan instruksi. Coba ulang tagihan yang sama.']);
            }
            $data = $response->json();
            $parser = app(PaymentInstructionService::class);
            $parser->validateResponse(new Payment(['reference' => $share->reference, 'amount' => $share->amount, 'method' => $share->method]), $data);
            $instructions = $parser->instructions($data);
            if (! $instructions && $data['transaction_status'] === 'pending') {
                throw ValidationException::withMessages(['payment' => 'Kode pembayaran belum tersedia. Coba lagi.']);
            }
            $share->update(['instructions' => $instructions]);
            $this->applyStatus($data);
        } finally {
            $lock->release();
        }
    }

    public function check(PaymentShare $share): void
    {
        if (! config('platform.midtrans_server_key')) {
            throw ValidationException::withMessages(['payment' => 'Pembayaran online belum aktif.']);
        }
        $response = Http::withBasicAuth(config('platform.midtrans_server_key'), '')->acceptJson()->connectTimeout(2)->timeout(5)->get(app(FinanceService::class)->apiBase().'/v2/'.rawurlencode($share->reference).'/status');
        if ($response->status() === 404 || (string) $response->json('status_code') === '404') {
            $share->update(['reconciled_at' => now()]);

            return;
        }
        if (! $response->successful() || ! is_array($response->json()) || $response->json('order_id') !== $share->reference) {
            throw ValidationException::withMessages(['payment' => 'Status belum dapat diverifikasi ke Midtrans.']);
        }
        $this->applyStatus($response->json());
        $share->update(['reconciled_at' => now()]);
    }

    /** @param array<string, mixed> $data */
    public function applyStatus(array $data): void
    {
        $initial = PaymentShare::where('reference', $data['order_id'] ?? '')->firstOrFail();
        $original = $initial->booking;
        DB::transaction(function () use ($data, $initial, $original): void {
            $original->trip()->lockForUpdate()->firstOrFail();
            $booking = Booking::whereKey($original->id)->lockForUpdate()->firstOrFail();
            $parent = Payment::where('booking_id', $booking->id)->lockForUpdate()->firstOrFail();
            $share = PaymentShare::whereKey($initial->id)->lockForUpdate()->firstOrFail();
            if (! is_string($data['transaction_id'] ?? null) || ! preg_match('/^([0-9]+)(?:\.0{1,2})?$/', (string) ($data['gross_amount'] ?? ''), $amount) || (int) $amount[1] !== $share->amount) {
                throw ValidationException::withMessages(['payment' => 'Nominal pembayaran anggota tidak sesuai.']);
            }
            $status = $data['transaction_status'] ?? '';
            if ($status === 'settlement' || ($status === 'capture' && ($data['fraud_status'] ?? '') === 'accept')) {
                if (in_array($share->status, ['paid', 'reconciliation_required', 'refunded'], true)) {
                    return;
                }
                $late = $booking->status !== 'awaiting_payment' || $booking->expires_at->isPast();
                $share->update(['status' => $late ? 'reconciliation_required' : 'paid', 'paid_at' => now(), 'provider_reference' => $data['transaction_id'], 'reconciled_at' => now()]);
                foreach (['cash' => ['gateway_cash', $share->amount], 'escrow' => ['customer_liability', -$share->amount]] as $suffix => [$account, $value]) {
                    LedgerEntry::firstOrCreate(['reference' => $share->reference.':receipt:'.$suffix], ['booking_id' => $booking->id, 'account' => $account, 'amount' => $value, 'description' => 'Pembayaran bagian '.$share->position]);
                }
                $received = PaymentShare::where('booking_id', $booking->id)->whereIn('status', ['paid', 'reconciliation_required'])->sum('amount');
                if (! $late && (int) $received === $booking->total) {
                    $booking->update(['status' => 'paid']);
                    $parent->update(['status' => 'paid', 'paid_at' => now(), 'provider_reference' => 'split:'.$booking->reference]);
                } elseif ($late) {
                    $parent->update(['status' => 'reconciliation_required']);
                    Refund::firstOrCreate(['booking_id' => $booking->id], ['user_id' => $booking->user_id, 'amount' => $received, 'status' => 'pending', 'reason' => 'Pembayaran split bill setelah reservasi ditutup']);
                    Refund::where('booking_id', $booking->id)->where('status', 'pending')->update(['amount' => $received]);
                }
                app(AuditService::class)->record('payment.share_received', $share, ['amount' => $share->amount]);
            } elseif (in_array($status, ['expire', 'cancel', 'deny'], true) && $share->status === 'pending') {
                $share->update(['status' => $status === 'expire' ? 'expired' : 'failed', 'reconciled_at' => now()]);
            } elseif (in_array($status, ['refund', 'partial_refund'], true) && $share->status === 'paid') {
                $share->update(['status' => 'reconciliation_required']);
                $parent->update(['status' => 'reconciliation_required']);
                app(AuditService::class)->record('payment.split_refund_requires_reconciliation', $share);
            }
        });
    }

    public function close(Booking $booking): void
    {
        $received = PaymentShare::where('booking_id', $booking->id)->whereIn('status', ['paid', 'reconciliation_required'])->sum('amount');
        PaymentShare::where('booking_id', $booking->id)->where('status', 'pending')->update(['status' => $booking->status]);
        if ($received > 0) {
            Payment::where('booking_id', $booking->id)->update(['status' => 'reconciliation_required']);
            Refund::firstOrCreate(['booking_id' => $booking->id], ['user_id' => $booking->user_id, 'amount' => $received, 'status' => 'pending', 'reason' => 'Split bill belum lunas saat reservasi ditutup; refund perlu diproses per transaksi anggota']);
        }
    }

    /** @return array<int, array<string, mixed>> */
    public function listing(Booking $booking): array
    {
        return PaymentShare::where('booking_id', $booking->id)->orderBy('position')->get()->map(fn (PaymentShare $share): array => [...$share->toArray(), 'share_url' => route('split.pay', $share->token)])->all();
    }
}
