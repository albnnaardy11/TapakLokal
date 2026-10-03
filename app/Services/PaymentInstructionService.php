<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Payment;
use App\Models\SouvenirOrder;
use App\Models\SouvenirPayment;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class PaymentInstructionService
{
    public function __construct(private PaymentMethodService $methods, private FinanceService $finance, private SouvenirCommerceService $commerce) {}

    public function start(Booking|SouvenirOrder $order, string $method): void
    {
        $this->methods->requireEnabled($method);
        if (! config('platform.midtrans_server_key')) {
            $this->reject('Pembayaran online belum aktif. Pesanan kamu tetap tersimpan.');
        }
        $isTrip = $order instanceof Booking;
        $lock = Cache::lock(($isTrip ? 'payment:checkout:' : 'souvenir:checkout:').$order->id, 90);
        if (! $lock->get()) {
            $this->reject('Instruksi pembayaran sedang disiapkan. Coba kembali.');
        }
        try {
            $order = $order->fresh();
            $payment = $order->payment()->firstOrFail();
            if ($order->status !== 'awaiting_payment' || $order->expires_at->isPast() || $payment->status !== 'pending') {
                $this->reject('Pesanan sudah tidak dapat dibayar.');
            }
            if ($payment->checkout_url) {
                $this->reject('Pembayaran ini sudah dibuat. Lanjutkan melalui tautan pembayaran yang tersimpan.');
            }
            if ($payment->method && $payment->method !== $method) {
                $this->reject('Metode sudah dikunci untuk pesanan ini. Gunakan instruksi pembayaran yang sama.');
            }
            if ($payment->instructions) {
                return;
            }
            $payment->update(['method' => $method]);
            try {
                $response = $this->client()->get($this->finance->apiBase().'/v2/'.rawurlencode($payment->reference).'/status');
                if ($response->status() === 404 || (string) $response->json('status_code') === '404') {
                    $request = [
                        'transaction_details' => ['order_id' => $payment->reference, 'gross_amount' => $payment->amount],
                        'customer_details' => ['first_name' => $order->contact_name, 'email' => $order->contact_email ?: ($isTrip ? $order->user->email : null), 'phone' => $order->contact_phone],
                        'custom_expiry' => ['order_time' => $order->created_at->timezone('Asia/Jakarta')->format('Y-m-d H:i:s O'), 'expiry_duration' => 30, 'unit' => 'minute'],
                        ...match ($method) {
                            'bca' => ['payment_type' => 'bank_transfer', 'bank_transfer' => ['bank' => 'bca']],
                            'mandiri' => ['payment_type' => 'echannel', 'echannel' => ['bill_info1' => 'Pesanan:', 'bill_info2' => 'TapakLokal']],
                            'alfamart', 'indomaret' => ['payment_type' => 'cstore', 'cstore' => ['store' => $method, 'message' => 'Pesanan TapakLokal']],
                            'ovo' => ['payment_type' => 'qris', 'qris' => ['acquirer' => 'gopay']],
                            'gopay' => ['payment_type' => 'gopay', 'gopay' => ['enable_callback' => true, 'callback_url' => route('checkout.payment', ['type' => $isTrip ? 'trip' : 'souvenir', 'id' => $order->id])]],
                        },
                    ];
                    $response = $this->client()->post($this->finance->apiBase().'/v2/charge', $request);
                    if ((string) $response->json('status_code') === '406') {
                        $response = $this->client()->get($this->finance->apiBase().'/v2/'.rawurlencode($payment->reference).'/status');
                    }
                }
            } catch (ConnectionException $exception) {
                report($exception);
                $this->reject('Penyedia pembayaran belum merespons. Coba kembali pada pesanan yang sama.');
            }
            if ($response->status() === 401 || (string) $response->json('status_code') === '401') {
                $this->reject('Midtrans menolak kunci sandbox. Hubungi bantuan untuk memperbaiki konfigurasi pembayaran; pesanan belum dibayar.');
            }
            if (! $response->successful() || ! in_array((string) $response->json('status_code'), ['200', '201'], true)) {
                $this->reject('Metode ini belum dapat digunakan. Hubungi bantuan atau coba kembali pada pesanan yang sama.');
            }
            $data = $response->json();
            if (! is_array($data)) {
                $this->reject('Respons pembayaran tidak valid.');
            }
            $this->validateResponse($payment, $data, $method);
            $instructions = $this->instructions($data);
            if (! $instructions && ($data['transaction_status'] ?? null) === 'pending') {
                $this->reject('Kode pembayaran belum tersedia. Coba kembali pada pesanan yang sama.');
            }
            DB::transaction(function () use ($payment, $instructions): void {
                $current = $payment->newQuery()->whereKey($payment->id)->lockForUpdate()->firstOrFail();
                $current->update(['instructions' => $instructions]);
            });
            if ($isTrip) {
                $this->finance->applyStatus($data);
            } else {
                $this->commerce->applyStatus($data);
            }
        } finally {
            $lock->release();
        }
    }

    private function client(): PendingRequest
    {
        return Http::withBasicAuth(config('platform.midtrans_server_key'), '')->acceptJson()->connectTimeout(2)->timeout(6);
    }

    /** @param array<string, mixed> $data */
    public function validateResponse(Payment|SouvenirPayment $payment, array $data, ?string $method = null): void
    {
        if (($data['order_id'] ?? null) !== $payment->reference || ! is_string($data['transaction_id'] ?? null) || ! is_string($data['transaction_status'] ?? null) || ! preg_match('/^([0-9]+)(?:\.0{1,2})?$/', (string) ($data['gross_amount'] ?? ''), $amount) || (int) $amount[1] !== $payment->amount) {
            $this->reject('Instruksi pembayaran tidak sesuai pesanan.');
        }
        $method ??= $payment->method;
        $type = $data['payment_type'] ?? null;
        $matches = match ($method) {
            'bca' => $type === 'bank_transfer' && collect(is_array($data['va_numbers'] ?? null) ? $data['va_numbers'] : [])->filter(fn ($value) => is_array($value))->contains(fn ($number) => ($number['bank'] ?? null) === 'bca'),
            'mandiri' => $type === 'echannel',
            'alfamart', 'indomaret' => $type === 'cstore' && ($data['store'] ?? null) === $method,
            'gopay' => in_array($type, ['gopay', 'qris'], true),
            'ovo' => $type === 'qris',
            default => false,
        };
        if (! $matches) {
            $this->reject('Metode pembayaran dari penyedia berbeda. Hubungi bantuan.');
        }
    }

    /** @param array<string, mixed> $data @return array<string, mixed> */
    public function instructions(array $data): array
    {
        $result = [];
        foreach (['payment_code', 'bill_key', 'biller_code'] as $field) {
            if (is_string($data[$field] ?? null) && preg_match('/^[0-9]{1,50}$/', $data[$field])) {
                $result[$field] = $data[$field];
            }
        }
        $va = collect(is_array($data['va_numbers'] ?? null) ? $data['va_numbers'] : [])->filter(fn ($value) => is_array($value))->first(fn ($value) => ($value['bank'] ?? '') === 'bca');
        if ($va && is_string($va['va_number'] ?? null) && preg_match('/^[0-9]{1,50}$/', $va['va_number'])) {
            $result['va_number'] = $va['va_number'];
        }
        foreach (array_slice(is_array($data['actions'] ?? null) ? $data['actions'] : [], 0, 10) as $action) {
            if (! is_array($action)) {
                continue;
            }
            $url = $action['url'] ?? '';
            if (! is_string($url)) {
                continue;
            }
            $host = parse_url($url, PHP_URL_HOST);
            if (! is_string($url) || parse_url($url, PHP_URL_SCHEME) !== 'https' || ! in_array($host, ['api.midtrans.com', 'api.sandbox.midtrans.com', 'simulator.sandbox.midtrans.com', 'gopay.co.id', 'www.gopay.co.id', 'gojek.link'], true)) {
                continue;
            }
            if (in_array($action['name'] ?? '', ['generate-qr-code', 'generate-qr-code-v2'], true)) {
                $result['qr_url'] = $url;
            }
            if (($action['name'] ?? '') === 'deeplink-redirect') {
                $result['app_url'] = $url;
            }
        }

        return $result;
    }

    private function reject(string $message): never
    {
        throw ValidationException::withMessages(['payment' => $message]);
    }
}
