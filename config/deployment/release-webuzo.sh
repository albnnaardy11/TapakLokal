#!/usr/bin/env bash
set -euo pipefail
umask 027
# Jalankan sebagai user situs dari checkout release baru, bukan root.
# PHP_BIN menunjuk PHP CLI 8.4 milik situs, APP_ROOT menunjuk checkout release.
# .env dan storage shared sudah harus tersedia; migrasi tidak menghapus data.
: "${PHP_BIN:?Set PHP_BIN to the absolute PHP 8.4 CLI executable}"
: "${APP_ROOT:?Set APP_ROOT to the absolute release checkout directory}"
test -x "$PHP_BIN"
cd "$APP_ROOT"
test -f artisan && test -f .env
test -w storage && test -w bootstrap/cache
"$PHP_BIN" -r 'exit(PHP_VERSION_ID >= 80400 ? 0 : 1);'
"$PHP_BIN" "${COMPOSER_BIN:?Set COMPOSER_BIN to composer.phar}" install --no-dev --prefer-dist --optimize-autoloader --no-interaction
# Build di CI/lokal lalu upload public/build agar npm tidak berebut RAM dengan layanan produksi.
test -f public/build/manifest.json
test ! -f public/hot
"$PHP_BIN" artisan config:clear --no-interaction
"$PHP_BIN" artisan migrate --force --no-interaction
"$PHP_BIN" artisan db:seed --class=PlatformSeeder --force --no-interaction
"$PHP_BIN" artisan config:cache --no-interaction
"$PHP_BIN" artisan route:cache --no-interaction
"$PHP_BIN" artisan view:cache --no-interaction
"$PHP_BIN" artisan platform:check --production --no-interaction
# Alihkan document root/current hanya setelah semua pemeriksaan lolos.
# Setelah aktivasi release: PHP_BIN artisan queue:restart; reload PHP-FPM dari panel Webuzo.
# Jangan generate ulang APP_KEY. Jalankan AdminAccountSeeder hanya saat bootstrap dengan akun produksi.
