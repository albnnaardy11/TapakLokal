<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\AccessService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminAccountSeeder extends Seeder
{
    public function run(AccessService $access): void
    {
        $access->seed();

        $accounts = [
            'super_admin' => [
                'name' => 'Super Admin',
                'email' => 'admin@tapaklokal.com',
                'password' => 'password',
            ],
            'content_admin' => [
                'name' => 'Content Admin',
                'email' => 'content.admin@tapaklokal.test',
                'password' => 'password',
            ],
            'operations_admin' => [
                'name' => 'Operations Admin',
                'email' => 'operations.admin@tapaklokal.test',
                'password' => 'password',
            ],
            'finance_admin' => [
                'name' => 'Finance Admin',
                'email' => 'finance.admin@tapaklokal.test',
                'password' => 'password',
            ],
            'growth_admin' => [
                'name' => 'Growth Admin',
                'email' => 'growth.admin@tapaklokal.test',
                'password' => 'password',
            ],
        ];

        // Tambahkan juga jika ada config admin_accounts
        $configAccounts = config('admin_accounts', []);
        foreach ($configAccounts as $role => $cfg) {
            $accounts[$role] = array_merge($accounts[$role] ?? [], $cfg);
        }

        foreach ($accounts as $role => $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password'] ?? 'password'),
                    'status' => 'active',
                    'email_verified_at' => now(),
                    'must_change_password' => false,
                ]
            );

            // Pastikan user aktif dan tidak dipaksa ubah password
            $user->update([
                'status' => 'active',
                'email_verified_at' => $user->email_verified_at ?? now(),
                'must_change_password' => false,
            ]);

            try {
                $access->grant($user, $role);
            } catch (\Throwable) {
                // Abaikan jika role sudah ada
            }
        }

        $this->command?->info('Akun admin berhasil dibuat dengan password default "password" (atau sesuai config).');
    }
}
