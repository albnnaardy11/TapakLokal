#!/usr/bin/env bash
set -euo pipefail
# Jalankan dari checkout release baru setelah .env dan storage ditautkan ke direktori shared.
# Persyaratan: PHP8.4 + GD/WebP/pgsql/mbstring/XML/curl/zip/pcntl, Composer2, Node22+, PostgreSQL, Redis.
# Pasang virtual host dan unit systemd secara terpisah setelah menyesuaikan domain/path.
test -f artisan && test -f .env
test -d storage/app/private
test -w storage && test -w bootstrap/cache
composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build
php artisan config:clear --no-interaction
php artisan migrate --force --no-interaction
php artisan db:seed --class=PlatformSeeder --force --no-interaction
php artisan config:cache --no-interaction
php artisan route:cache --no-interaction
php artisan view:cache --no-interaction
php artisan platform:check --production --no-interaction
# Exit nonzero berarti release belum boleh dialihkan menjadi current.
# Setelah symlink current dialihkan, jalankan dari current:
# php artisan queue:restart --no-interaction
# sudo systemctl reload php8.4-fpm
# sudo systemctl enable --now tapaklokal-worker.service tapaklokal-scheduler.timer
# AdminAccountSeeder hanya dijalankan saat bootstrap dengan kredensial produksi eksplisit.
# Pertahankan APP_KEY, database, dan storage shared antar-release; jangan gunakan migrate:fresh.
