<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\AccessService;
use App\Services\AuditService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use RuntimeException;

class AdminAccountSeeder extends Seeder
{
    public function run(AccessService $access, AuditService $audit): void
    {
        $local = app()->environment(['local', 'testing']);
        $accounts = config('admin_accounts');
        $emails = array_column($accounts, 'email');
        if (count(array_unique($emails)) !== count($emails)) {
            throw new RuntimeException('Setiap role admin harus memakai email berbeda.');
        }
        foreach ($accounts as $role => &$account) {
            $existing = User::where('email', $account['email'])->first();
            if ($existing) {
                if (! $existing->roles()->where('name', $role)->exists()) {
                    throw new RuntimeException('Email admin sudah dipakai akun dengan role berbeda: '.$account['email']);
                }

                continue;
            }
            if (! $local && (str_ends_with($account['email'], '.test') || empty($account['password']))) {
                throw new RuntimeException('Konfigurasi email dan password admin produksi wajib diisi untuk '.$role.'.');
            }
            $account['password'] = $account['password'] ?: Str::password(24);
            Validator::make($account, ['name' => ['required', 'string', 'max:100'], 'email' => ['required', 'email', 'max:255'], 'password' => ['required', 'string', Password::min(12)->letters()->numbers()->mixedCase()->symbols()]])->validate();
        }
        unset($account);
        $created = DB::transaction(function () use ($accounts, $access, $audit): array {
            $access->seed();
            $created = [];
            foreach ($accounts as $role => $account) {
                if (User::where('email', $account['email'])->exists()) {
                    continue;
                }
                $user = User::create($account);
                $user->forceFill(['must_change_password' => true])->save();
                $access->grant($user, $role);
                $audit->record('admin.account_seeded', $user, ['role' => $role], $user->id);
                $created[] = ['role' => $role, 'email' => $account['email'], 'password' => $account['password']];
            }

            return $created;
        });
        if ($local && count($created)) {
            $path = 'bootstrap/admin-credentials-'.now()->format('Ymd-His').'-'.Str::lower(Str::random(6)).'.json';
            if (! Storage::disk('local')->put($path, json_encode($created, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES))) {
                throw new RuntimeException('Akun dibuat, tetapi berkas kredensial gagal disimpan. Gunakan reset password sebelum login.');
            }
            $this->command?->info('Kredensial lokal tersimpan di: '.Storage::disk('local')->path($path));
        }
        $this->command?->info(count($created).' akun admin dibuat. Akun lama, password, dan role tidak diubah.');
    }
}
