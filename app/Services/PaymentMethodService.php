<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentMethodService
{
    /** @return array<int, array<string, mixed>> */
    public function catalog(): array
    {
        $methods = [
            ['id' => 'gopay', 'name' => 'GoPay', 'group' => 'Dompet digital', 'description' => 'Bayar melalui aplikasi GoPay atau QRIS', 'badge' => 'GoPay'],
            ['id' => 'ovo', 'name' => 'OVO melalui QRIS', 'group' => 'Dompet digital', 'description' => 'Pindai QRIS dari aplikasi OVO', 'badge' => 'OVO'],
            ['id' => 'bca', 'name' => 'BCA Virtual Account', 'group' => 'Transfer bank', 'description' => 'Transfer ke nomor virtual account pesanan', 'badge' => 'BCA'],
            ['id' => 'mandiri', 'name' => 'Mandiri Bill Payment', 'group' => 'Transfer bank', 'description' => 'Bayar dengan kode perusahaan dan kode pembayaran', 'badge' => 'mandiri'],
            ['id' => 'alfamart', 'name' => 'Alfamart / Alfamidi', 'group' => 'Gerai retail', 'description' => 'Tunjukkan kode pembayaran kepada kasir', 'badge' => 'Alfamart'],
            ['id' => 'indomaret', 'name' => 'Indomaret', 'group' => 'Gerai retail', 'description' => 'Bayar menggunakan kode pembayaran di gerai', 'badge' => 'Indomaret'],
            ['id' => 'wallet', 'name' => 'TapakLokal Wallet', 'group' => 'Saldo akun', 'description' => 'Saldo belum tersedia untuk pembayaran', 'badge' => 'TapakLokal'],
        ];

        return array_map(fn ($method) => [...$method, 'logo' => $method['id'] === 'wallet' ? null : '/Assets/Images/logo-bank/'.($method['id'] === 'alfamart' ? 'alfa' : $method['id']).'.webp', 'enabled' => $method['id'] !== 'wallet' && in_array($method['id'], config('platform.payment_methods'), true)], $methods);
    }

    /** @return array<string, mixed> */
    public function requireEnabled(string $method): array
    {
        $item = collect($this->catalog())->firstWhere('id', $method);
        if (! $item || ! $item['enabled']) {
            throw ValidationException::withMessages(['method' => 'Metode pembayaran tidak tersedia.']);
        }

        return $item;
    }

    /** @return array<string, mixed> */
    public function preferences(User $user): array
    {
        return $user->payment_preferences ?? ['saved' => [], 'primary' => null];
    }

    /** @param array<int, string> $saved */
    public function save(User $user, array $saved, ?string $primary): void
    {
        foreach ($saved as $method) {
            $this->requireEnabled($method);
        }
        if ($primary && ! in_array($primary, $saved, true)) {
            throw ValidationException::withMessages(['primary' => 'Metode utama harus termasuk pilihan tersimpan.']);
        }
        DB::transaction(function () use ($user, $saved, $primary): void {
            $current = User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            $current->payment_preferences = ['saved' => array_values(array_unique($saved)), 'primary' => $primary];
            $current->save();
        });
    }
}
