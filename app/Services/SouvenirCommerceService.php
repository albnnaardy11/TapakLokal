<?php

namespace App\Services;

use App\Models\SouvenirCartItem;
use App\Models\SouvenirOrder;
use App\Models\SouvenirPayment;
use App\Models\SouvenirProduct;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SouvenirCommerceService
{
    public function __construct(private AuditService $audit) {}

    /** @param array<string, mixed> $data */
    public function createOrder(User $user, array $data): SouvenirOrder
    {
        $hash = hash('sha256', json_encode($data, JSON_THROW_ON_ERROR));

        return DB::transaction(function () use ($user, $data, $hash): SouvenirOrder {
            User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $existing = SouvenirOrder::where('user_id', $user->id)->where('idempotency_key', $data['idempotency_key'])->first();
            if ($existing) {
                if (! hash_equals($existing->request_hash, $hash)) {
                    throw ValidationException::withMessages(['order' => 'Kunci pesanan telah digunakan untuk permintaan berbeda.']);
                }

                return $existing;
            }
            $lines = SouvenirCartItem::where('user_id', $user->id)->whereHas('product', fn ($q) => $q->where('vendor_id', $data['vendor_id']))->orderBy('souvenir_product_id')->get();
            if ($lines->isEmpty() || $lines->count() > 50) {
                throw ValidationException::withMessages(['order' => 'Keranjang toko kosong atau melebihi 50 baris.']);
            }
            $products = SouvenirProduct::whereIn('id', $lines->pluck('souvenir_product_id'))->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $vendor = Vendor::whereKey($data['vendor_id'])->lockForUpdate()->firstOrFail();
            abort_unless($vendor->status === 'verified' && User::whereKey($vendor->user_id)->where('status', 'active')->exists(), 403);
            $subtotal = $vendorAmount = $preparationDays = 0;
            $quantities = $lines->groupBy('souvenir_product_id')->map(fn ($items) => $items->sum('quantity'));
            foreach ($products as $product) {
                if ($product->status !== 'published' || $quantities[$product->id] > $product->stock - $product->reserved_stock) {
                    throw ValidationException::withMessages(['order' => 'Produk tidak tersedia atau stok berubah. Periksa keranjang.']);
                }
                $preparationDays = max($preparationDays, $product->preparation_days);
            }
            foreach ($lines as $line) {
                $product = $products[$line->souvenir_product_id];
                if (! in_array($line->variant, $product->variants, true)) {
                    throw ValidationException::withMessages(['order' => 'Varian produk telah berubah. Tambahkan kembali produk.']);
                }
                $subtotal += $product->sellingPrice() * $line->quantity;
                $vendorAmount += $product->price * $line->quantity;
            }
            $shippingFee = 0;
            $address = $products->first()->pickup_address;
            $service = null;
            if ($data['method'] === 'pickup') {
                if (collect($products)->pluck('pickup_address')->unique()->count() !== 1 || ($data['pickup_date'] ?? '') < now()->addDays($preparationDays)->toDateString()) {
                    throw ValidationException::withMessages(['pickup_date' => 'Jadwal atau lokasi pengambilan tidak sesuai waktu persiapan produk.']);
                }
            } else {
                $service = $data['delivery_service'];
                $address = $data['address'];
                foreach ($products as $product) {
                    $rate = collect($product->delivery_rates ?? [])->first(fn ($rate) => $rate['service'] === $service && $rate['region'] === $data['delivery_region']);
                    if ($product->pickup_only || ! $rate) {
                        throw ValidationException::withMessages(['delivery_service' => 'Pengiriman belum tersedia untuk produk atau daerah ini. Pilih pengambilan di tempat.']);
                    }
                    $shippingFee += $rate['fee'] * $quantities[$product->id];
                }
            }
            $order = SouvenirOrder::create([
                'reference' => 'SO-'.Str::ulid(), 'user_id' => $user->id, 'vendor_id' => $vendor->id,
                'idempotency_key' => $data['idempotency_key'], 'request_hash' => $hash,
                'contact_name' => $data['contact_name'], 'contact_phone' => $data['contact_phone'],
                'method' => $data['method'], 'address' => $address, 'pickup_date' => $data['method'] === 'pickup' ? $data['pickup_date'] : null,
                'delivery_service' => $service, 'shipping_fee' => $shippingFee, 'subtotal' => $subtotal,
                'total' => $subtotal + $shippingFee, 'vendor_amount' => $vendorAmount + $shippingFee,
                'platform_fee' => $subtotal - $vendorAmount, 'status' => 'awaiting_payment', 'expires_at' => now()->addMinutes(30),
                'contact_email' => $data['contact_email'] ?? $user->email,
            ]);
            foreach ($lines as $line) {
                $product = $products[$line->souvenir_product_id];
                $order->items()->create(['souvenir_product_id' => $product->id, 'name' => $product->name, 'variant' => $line->variant, 'quantity' => $line->quantity, 'unit_price' => $product->sellingPrice(), 'vendor_price' => $product->price, 'note' => $line->note]);
            }
            foreach ($products as $product) {
                $product->increment('reserved_stock', $quantities[$product->id]);
            }
            SouvenirPayment::create(['souvenir_order_id' => $order->id, 'reference' => $order->reference, 'amount' => $order->total, 'status' => 'pending', 'reconciled_at' => now()]);
            SouvenirCartItem::whereIn('id', $lines->pluck('id'))->delete();
            $this->audit->record('souvenir.order.created', $order);

            return $order;
        }, 3);
    }

    public function checkout(SouvenirOrder $order): string
    {
        if (! config('platform.midtrans_server_key')) {
            throw ValidationException::withMessages(['payment' => 'Midtrans belum diaktifkan. Pesanan tetap tersimpan.']);
        }
        $lock = Cache::lock('souvenir:checkout:'.$order->id, 45);
        if (! $lock->get()) {
            throw ValidationException::withMessages(['payment' => 'Pembayaran sedang disiapkan. Coba kembali.']);
        }
        try {
            $order->refresh()->load('payment');
            if ($order->payment->method) {
                throw ValidationException::withMessages(['payment' => 'Lanjutkan instruksi pembayaran pada halaman pembayaran pesanan.']);
            }
            if ($order->status !== 'awaiting_payment' || $order->expires_at->isPast()) {
                throw ValidationException::withMessages(['payment' => 'Pesanan tidak dapat dibayar.']);
            }
            if ($order->payment->checkout_url) {
                return $order->payment->checkout_url;
            }
            $base = config('platform.midtrans_production') ? 'https://app.midtrans.com' : 'https://app.sandbox.midtrans.com';
            try {
                $response = Http::withBasicAuth(config('platform.midtrans_server_key'), '')->acceptJson()->connectTimeout(5)->timeout(15)->post($base.'/snap/v1/transactions', [
                    'transaction_details' => ['order_id' => $order->reference, 'gross_amount' => $order->total],
                    'customer_details' => ['first_name' => $order->contact_name, 'phone' => $order->contact_phone],
                    'expiry' => ['start_time' => $order->created_at->format('Y-m-d H:i:s O'), 'unit' => 'minutes', 'duration' => 30],
                    'callbacks' => ['finish' => route('souvenirs.orders.show', $order)],
                ]);
            } catch (ConnectionException $exception) {
                report($exception);
                throw ValidationException::withMessages(['payment' => 'Midtrans belum merespons. Coba kembali dari pesanan yang sama.']);
            }
            $url = $response->json('redirect_url');
            if (! $response->successful() || ! is_string($url) || parse_url($url, PHP_URL_SCHEME) !== 'https' || ! in_array(parse_url($url, PHP_URL_HOST), ['app.midtrans.com', 'app.sandbox.midtrans.com'], true)) {
                throw ValidationException::withMessages(['payment' => 'Tautan pembayaran belum tersedia. Hubungi dukungan dengan nomor pesanan.']);
            }
            $order->payment->update(['checkout_url' => $url]);
            $order->refresh();
            if ($order->status !== 'awaiting_payment' || $order->expires_at->isPast()) {
                throw ValidationException::withMessages(['payment' => 'Pesanan sudah berubah. Muat ulang halaman.']);
            }

            return $url;
        } finally {
            $lock->release();
        }
    }

    /** @param array<string, mixed> $data */
    public function applyStatus(array $data): void
    {
        $payment = SouvenirPayment::where('reference', $data['order_id'])->firstOrFail();
        $this->lockedOrder($payment->souvenir_order_id, function (SouvenirOrder $order, $products) use ($data): void {
            $payment = SouvenirPayment::where('souvenir_order_id', $order->id)->lockForUpdate()->firstOrFail();
            if (! preg_match('/^([0-9]+)(?:\.0{1,2})?$/', (string) ($data['gross_amount'] ?? ''), $amount) || (int) $amount[1] !== $payment->amount) {
                throw ValidationException::withMessages(['payment' => 'Nominal pembayaran berbeda dari pesanan.']);
            }
            $settled = $data['transaction_status'] === 'settlement' || ($data['transaction_status'] === 'capture' && ($data['fraud_status'] ?? '') === 'accept');
            if ($settled && ! in_array($payment->status, ['paid', 'refunded', 'reconciliation_required'], true)) {
                $late = $order->status !== 'awaiting_payment' || $order->expires_at->isPast();
                if ($order->status === 'awaiting_payment') {
                    $this->releaseStock($order, $products, ! $late);
                    $order->update(['status' => $late ? 'expired' : 'paid']);
                }
                $payment->update(['status' => $late ? 'reconciliation_required' : 'paid', 'provider_reference' => $data['transaction_id'], 'paid_at' => now()]);
                $this->ledger($order, 'receipt', ['gateway_cash' => $order->total, 'customer_liability' => -$order->total]);
            } elseif (in_array($data['transaction_status'], ['expire', 'deny', 'cancel'], true) && $payment->status === 'pending') {
                if ($order->status === 'awaiting_payment') {
                    $this->releaseStock($order, $products);
                    $order->update(['status' => 'expired']);
                }
                $payment->update(['status' => 'failed']);
            } elseif (in_array($data['transaction_status'], ['refund', 'partial_refund'], true) && $payment->status === 'paid') {
                $payment->update(['status' => 'reconciliation_required']);
                $this->audit->record('souvenir.payment.refund_requires_reconciliation', $order);
            }
        });
    }

    public function transition(SouvenirOrder $order, string $status, ?string $tracking = null): void
    {
        $this->lockedOrder($order->id, function (SouvenirOrder $order, $products) use ($status, $tracking): void {
            $payment = SouvenirPayment::where('souvenir_order_id', $order->id)->lockForUpdate()->firstOrFail();
            if (! in_array($status, ['cancelled', 'expired'], true) && $payment->status !== 'paid') {
                throw ValidationException::withMessages(['status' => 'Pembayaran belum terverifikasi atau memerlukan pemeriksaan keuangan.']);
            }
            $allowed = ['awaiting_payment' => ['cancelled', 'expired'], 'paid' => ['processing'], 'processing' => [$order->method === 'pickup' ? 'ready_for_pickup' : 'shipped'], 'shipped' => ['completed'], 'ready_for_pickup' => ['completed']];
            if (! in_array($status, $allowed[$order->status] ?? [], true) || ($status === 'expired' && $order->expires_at->isFuture())) {
                throw ValidationException::withMessages(['status' => 'Perubahan status pesanan tidak diizinkan.']);
            }
            if ($status === 'shipped' && ! filled($tracking)) {
                throw ValidationException::withMessages(['tracking_number' => 'Nomor resi diperlukan.']);
            }
            if ($order->status === 'awaiting_payment') {
                $this->releaseStock($order, $products);
            }
            $order->update(['status' => $status, 'tracking_number' => $tracking ?: $order->tracking_number, 'completed_at' => $status === 'completed' ? now() : null]);
            if ($status === 'completed') {
                $this->ledger($order, 'completion', ['customer_liability' => $order->total, 'vendor_payable' => -$order->vendor_amount, 'platform_revenue' => -$order->platform_fee]);
            }
            $this->audit->record('souvenir.order.'.$status, $order);
        });
    }

    private function lockedOrder(int $id, callable $action): void
    {
        $ids = DB::table('souvenir_order_items')->where('souvenir_order_id', $id)->pluck('souvenir_product_id');
        DB::transaction(function () use ($id, $ids, $action): void {
            $products = SouvenirProduct::whereIn('id', $ids)->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $order = SouvenirOrder::whereKey($id)->lockForUpdate()->firstOrFail();
            $order->load('items');
            $action($order, $products);
        }, 3);
    }

    private function releaseStock(SouvenirOrder $order, Collection $products, bool $sold = false): void
    {
        foreach ($order->items as $item) {
            $product = $products[$item->souvenir_product_id];
            $product->decrement('reserved_stock', $item->quantity);
            if ($sold) {
                $product->decrement('stock', $item->quantity);
            }
        }
    }

    /** @param array<string, int> $entries */
    private function ledger(SouvenirOrder $order, string $event, array $entries): void
    {
        foreach ($entries as $account => $amount) {
            DB::table('souvenir_ledger_entries')->insertOrIgnore(['souvenir_order_id' => $order->id, 'reference' => $order->reference.':'.$event.':'.$account, 'account' => $account, 'amount' => $amount, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
