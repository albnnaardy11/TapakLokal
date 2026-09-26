<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CheckPlatformReadiness extends Command
{
    protected $signature = 'platform:check {--production : Periksa persyaratan konfigurasi produksi}';

    protected $description = 'Periksa koneksi, migrasi, build, storage, dan konfigurasi produksi tanpa menampilkan rahasia';

    public function handle(): int
    {
        $checks = [];
        $check = function (string $label, callable $probe) use (&$checks): void {
            try {
                $checks[] = [$probe() ? 'OK' : 'FAIL', $label];
            } catch (\Throwable) {
                $checks[] = ['FAIL', $label];
            }
        };
        $check('PHP >= 8.4; GD WebP, PDO, mbstring, OpenSSL', fn () => PHP_VERSION_ID >= 80400 && extension_loaded('gd') && function_exists('imagewebp') && extension_loaded('pdo') && extension_loaded('mbstring') && extension_loaded('openssl'));
        $check('Database dapat diakses', fn () => (bool) DB::connection()->getPdo());
        $check('Semua migrasi sudah dijalankan', function (): bool {
            $migrator = app('migrator');

            return $migrator->repositoryExists() && count(array_diff(array_keys($migrator->getMigrationFiles(database_path('migrations'))), $migrator->getRepository()->getRan())) === 0;
        });
        $check('Cache dapat ditulis dan dibaca', function (): bool {
            $key = 'readiness:'.Str::uuid();
            try {
                Cache::put($key, 'ok', 10);

                return Cache::get($key) === 'ok';
            } finally {
                Cache::forget($key);
            }
        });
        $check('Storage privat dapat ditulis dan dibaca', function (): bool {
            $key = 'readiness/'.Str::uuid();
            try {
                return Storage::disk('local')->put($key, 'ok') && Storage::disk('local')->get($key) === 'ok';
            } finally {
                Storage::disk('local')->delete($key);
            }
        });
        $check('Build frontend tersedia; dev server Vite tidak aktif', fn () => is_file(public_path('build/manifest.json')) && ! is_file(public_path('hot')));
        $check('Lima role admin memiliki akun aktif', function (): bool {
            foreach (array_keys(config('admin_accounts')) as $role) {
                if (! User::where('status', 'active')->whereHas('roles', fn ($query) => $query->where('name', $role))->exists()) {
                    return false;
                }
            }

            return true;
        });
        if ($this->option('production')) {
            $check('APP_ENV=production dan APP_DEBUG=false', fn () => app()->environment('production') && ! config('app.debug'));
            $check('APP_KEY tersedia dan URL HTTPS bukan domain contoh', fn () => filled(config('app.key')) && str_starts_with(config('app.url'), 'https://') && ! Str::contains(config('app.url'), ['localhost', 'example.', '.test']));
            $check('Cookie sesi secure dan HTTP-only', fn () => config('session.secure') && config('session.http_only'));
            $check('Cache dan sesi memakai Redis', fn () => config('cache.default') === 'redis' && config('session.driver') === 'redis');
            $check('Antrean pembayaran dan aplikasi persisten', fn () => in_array(config('queue.default'), ['database', 'redis']) && in_array(config('platform.payment_queue_connection'), ['database', 'redis']));
            $check('Email memakai transport SMTP atau provider produksi', fn () => in_array(config('mail.default'), ['smtp', 'ses', 'postmark', 'resend', 'mailgun']));
            $check('Midtrans produksi terkonfigurasi', fn () => config('platform.midtrans_production') && filled(config('platform.midtrans_server_key')));
            $check('Markup platform dalam rentang 8–15%', fn () => config('platform.markup_bps') >= 800 && config('platform.markup_bps') <= 1500);
            $check('Tidak ada akun admin dengan email demo', fn () => ! User::whereHas('roles', fn ($q) => $q->whereIn('name', array_keys(config('admin_accounts'))))->where('email', 'like', '%.test')->exists());
        }
        $this->table(['Status', 'Pemeriksaan'], $checks);
        $this->line('Pemeriksaan ini tidak membuktikan worker, TLS, pengiriman email, transaksi gateway, backup restore, atau kapasitas beban. Verifikasi end-to-end di VPS tetap diperlukan.');

        return collect($checks)->contains(fn (array $row) => $row[0] === 'FAIL') ? self::FAILURE : self::SUCCESS;
    }
}
