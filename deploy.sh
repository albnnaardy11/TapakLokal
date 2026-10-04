#!/usr/bin/env bash
# TapakLokal / Ubuntu + Webuzo. Run as the domain owner, never root.
# Upload the project, production .env, database and storage before deployment.
# PHP_BIN=/absolute/path/php COMPOSER_BIN=/absolute/path/composer bash deploy.sh
# BUILD_FRONTEND=0 uses public/build uploaded from npm run build on your computer.
set -Eeuo pipefail
umask 027

fail() { printf 'ERROR: %s\n' "$*" >&2; exit 1; }
usage() {
    cat <<'HELP'
Usage: bash deploy.sh [--check|--init-env|--help]
  --check     Check prerequisites only; no installation or database changes.
  --init-env  Copy the Webuzo production template if .env does not exist.
  no option   Install dependencies, build, migrate, check production and activate.
Variables:
  PHP_BIN         PHP 8.4+ CLI executable (default: php from PATH)
  COMPOSER_BIN    Composer 2 PHP executable/phar (default: composer from PATH)
  BUILD_FRONTEND  1 = npm ci + build (default), 0 = use uploaded public/build
Run from the uploaded project as its Webuzo user. Domain document root: public/.
Before an update, back up database and storage. This is an in-place deployment:
maintenance mode stays enabled on failure. There is no automatic DB rollback.
For existing/imported data, retain the original APP_KEY. For a NEW database only,
run PHP_BIN artisan key:generate --force after Composer has been installed.
Redis is installed if absent (sudo required). Queue worker and scheduler systemd
units are installed automatically. Do not also install cron/worker templates
for this application. Accounts, DB import, DNS and TLS need initial setup.
Set QUEUE_CONNECTION=redis and PAYMENT_QUEUE_CONNECTION=redis.
HELP
}
mode="${1:-deploy}"
case "$mode" in --help|-h) usage; exit 0;; deploy|--check|--init-env) ;; *) usage; exit 1;; esac
[[ $# -le 1 ]] || fail 'Too many arguments.'
[[ "${EUID}" -ne 0 ]] || fail 'Use the Webuzo domain owner, not root.'
app_root="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)"
cd "$app_root"
[[ -f artisan && -f composer.lock && -f package-lock.json ]] || fail 'Incomplete project upload.'
if [[ "$mode" == --init-env ]]; then
    [[ ! -e .env ]] || fail '.env already exists; it was not changed.'
    (set -o noclobber; cat config/deployment/webuzo.env.example > .env)
    chmod 600 .env
    printf 'Created .env. Configure domain, APP_KEY, DB, Redis, SMTP and Midtrans. For PostgreSQL use DB_CONNECTION=pgsql and DB_PORT=5432.\n'
    exit 0
fi
export PATH="/usr/local/bin:/usr/local/apps/nodejs/bin:$PATH"
php_bin="${PHP_BIN:-}"
if [[ -z "$php_bin" ]]; then
    for candidate in /usr/local/apps/php84/bin/php /usr/local/apps/php85/bin/php "$(command -v php || true)"; do
        if [[ -x "$candidate" ]] && "$candidate" -r 'exit(PHP_VERSION_ID >= 80400 ? 0 : 1);'; then
            php_bin="$candidate"
            break
        fi
    done
fi
composer_bin="${COMPOSER_BIN:-$(command -v composer || true)}"
[[ -x "$php_bin" ]] || fail 'Set PHP_BIN to the PHP CLI executable used by your Webuzo domain.'
[[ -f "$composer_bin" ]] || fail 'Install Composer 2 or set COMPOSER_BIN to composer.phar.'
export PATH="$(dirname "$php_bin"):$PATH"
"$php_bin" -r '
if (PHP_VERSION_ID < 80400) { fwrite(STDERR, "PHP 8.4+ required.\n"); exit(1); }
foreach (["ctype","curl","dom","fileinfo","filter","gd","hash","mbstring","openssl","pcre","pdo","session","tokenizer","xml","zip","pcntl","posix"] as $ext) {
    if (!extension_loaded($ext)) { fwrite(STDERR, "Missing PHP extension: $ext\n"); exit(1); }
}
if (!function_exists("imagewebp") || (!extension_loaded("pdo_pgsql") && !extension_loaded("pdo_mysql"))) { fwrite(STDERR, "GD WebP and PDO PostgreSQL/MySQL required.\n"); exit(1); }
'
[[ "$("$php_bin" "$composer_bin" --version --no-ansi)" == *"Composer version 2."* ]] || fail 'Composer 2 required.'
[[ -f .env ]] || fail 'Run bash deploy.sh --init-env, then configure .env.'
[[ ! -e public/hot ]] || fail 'Remove the uploaded public/hot dev-server marker before deploying.'
[[ -d storage && -w storage && -d bootstrap/cache && -w bootstrap/cache ]] || fail 'The site user must be able to write storage and bootstrap/cache.'
build_frontend="${BUILD_FRONTEND:-1}"
case "$build_frontend" in
    1)
        command -v npm >/dev/null || fail 'Install Node.js 22.12+ (or 24 LTS) with npm.'
        node -e 'const [a,b]=process.versions.node.split(".").map(Number); if(!((a===22&&b>=12)||a>=24)) process.exit(1)' || fail 'Use Node.js 22.12+ or 24 LTS.'
        ;;
    0) [[ -s public/build/manifest.json ]] || fail 'Upload public/build from a completed npm run build.';;
    *) fail 'BUILD_FRONTEND must be 0 or 1.';;
esac
command -v flock >/dev/null || fail 'Install Ubuntu util-linux (flock).'
[[ "$mode" != --check ]] || { printf 'Prerequisites OK. Database and production configuration are checked during deployment.\n'; exit 0; }
exec 9>storage/deploy.lock
flock -n 9 || fail 'Another deployment is running.'
command -v sudo >/dev/null || fail 'sudo is required to install Redis and configure workers.'
command -v systemctl >/dev/null || fail 'Ubuntu systemd is required.'
[[ "$app_root" =~ ^/[a-zA-Z0-9_./-]+$ && "$php_bin" =~ ^/[a-zA-Z0-9_./-]+$ ]] || fail 'Project and PHP paths must not contain spaces or special characters.'
sudo -v
# Preserve existing Webuzo Redis installations; install only when absent.
if systemctl cat redis-server.service >/dev/null 2>&1; then
    sudo systemctl enable --now redis-server
elif ! command -v redis-server >/dev/null && [[ ! -x /usr/local/apps/redis/bin/redis-server ]]; then
    command -v ss >/dev/null || fail 'Install iproute2 to inspect existing Redis listeners.'
    if ! ss -ltn | grep -qE ':6379[[:space:]]'; then
        sudo apt-get update
        sudo apt-get install -y redis-server
        sudo systemctl enable --now redis-server
    fi
fi
maintenance_started=0
trap 'code=$?; if (( code != 0 )); then printf "Deployment stopped (exit %s).\n" "$code" >&2; if (( maintenance_started )); then printf "Maintenance remains ON. Fix the failure and rerun deploy.sh; do not expose an incomplete release.\n" >&2; fi; fi' EXIT
# Existing installations go down before vendor/build files change.
if [[ -f vendor/autoload.php ]]; then
    "$php_bin" artisan down --retry=60 --no-interaction
    maintenance_started=1
fi
"$php_bin" "$composer_bin" install --no-dev --prefer-dist --optimize-autoloader --no-interaction
"$php_bin" "$composer_bin" check-platform-reqs --no-dev
"$php_bin" artisan config:clear --no-interaction
# Validate environment without printing secrets. Never generate/rotate APP_KEY here.
"$php_bin" <<'PHP'
<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
if (!$app->environment('production') || config('app.debug') || !config('app.key') || !str_starts_with(config('app.url'), 'https://') || str_contains(config('app.url'), 'DOMAIN_ANDA')) {
    fwrite(STDERR, "Configure APP_ENV=production, APP_DEBUG=false, HTTPS APP_URL and APP_KEY before deployment. Preserve the old key for imported data.\n"); exit(1);
}
try {
    Illuminate\Support\Facades\DB::connection()->getPdo();
    Illuminate\Support\Facades\Redis::connection()->ping();
    Illuminate\Support\Facades\Redis::connection('cache')->ping();
} catch (Throwable $exception) {
    fwrite(STDERR, "Database/Redis connection failed. Check DB_* and REDIS_* in .env.\n"); exit(1);
}
if (config('queue.default') !== 'redis' || config('platform.payment_queue_connection') !== 'redis') {
    fwrite(STDERR, "Set QUEUE_CONNECTION=redis and PAYMENT_QUEUE_CONNECTION=redis for the automatic worker.\n"); exit(1);
}
PHP
if (( ! maintenance_started )); then
    "$php_bin" artisan down --retry=60 --no-interaction
    maintenance_started=1
fi
if [[ "$build_frontend" == 1 ]]; then
    npm ci --include=dev --no-audit --no-fund
    npm run build
fi
[[ -s public/build/manifest.json ]] || fail 'Frontend manifest missing.'
"$php_bin" artisan storage:link --no-interaction
[[ -d public/storage && "$(readlink -f public/storage)" == "$(readlink -f storage/app/public)" ]] || fail 'public/storage must link to storage/app/public.'
"$php_bin" artisan migrate --force --no-interaction
"$php_bin" artisan db:seed --class=PlatformSeeder --force --no-interaction
"$php_bin" artisan config:cache --no-interaction
"$php_bin" artisan route:cache --no-interaction
"$php_bin" artisan view:cache --no-interaction
"$php_bin" artisan platform:check --production --no-interaction
site_user="$(id -un)"
service_id="tapaklokal-$(printf '%s' "$app_root" | sha256sum | cut -c1-12)"
unit_temp="$(mktemp -d)"
cat > "$unit_temp/$service_id-worker.service" <<UNIT
[Unit]
Description=TapakLokal queue
After=network-online.target
[Service]
User=$site_user
WorkingDirectory=$app_root
ExecStart=$php_bin artisan queue:work redis --queue=default --sleep=2 --tries=6 --timeout=60 --max-time=3600 --memory=192
Restart=always
RestartSec=5
TimeoutStopSec=90
UMask=0027
[Install]
WantedBy=multi-user.target
UNIT
cat > "$unit_temp/$service_id-scheduler.service" <<UNIT
[Unit]
Description=TapakLokal scheduler
[Service]
Type=oneshot
User=$site_user
WorkingDirectory=$app_root
ExecStart=$php_bin artisan schedule:run
UMask=0027
UNIT
cat > "$unit_temp/$service_id-scheduler.timer" <<UNIT
[Unit]
Description=TapakLokal minute scheduler
[Timer]
OnCalendar=*-*-* *:*:00
AccuracySec=1s
[Install]
WantedBy=timers.target
UNIT
sudo install -m 644 "$unit_temp/$service_id-worker.service" "$unit_temp/$service_id-scheduler.service" "$unit_temp/$service_id-scheduler.timer" /etc/systemd/system/
rm -- "$unit_temp/$service_id-worker.service" "$unit_temp/$service_id-scheduler.service" "$unit_temp/$service_id-scheduler.timer"
rmdir -- "$unit_temp"
sudo systemctl daemon-reload
"$php_bin" artisan queue:restart --no-interaction
sudo systemctl enable --now "$service_id-worker.service" "$service_id-scheduler.timer"
sudo systemctl restart "$service_id-worker.service"
sudo systemctl is-active --quiet "$service_id-worker.service" "$service_id-scheduler.timer"
"$php_bin" artisan up --no-interaction
maintenance_started=0
printf 'Deployment complete. Verify HTTPS /up and login, uploads, email and payments.\nReload this domain PHP-FPM in Webuzo; ensure queue workers and scheduler are running.\n'
