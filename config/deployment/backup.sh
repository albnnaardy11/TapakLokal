#!/usr/bin/env bash
set -euo pipefail
umask 077
# Jalankan sebagai user backup dengan akses baca storage dan PGSERVICE=tapaklokal.
# Konfigurasi koneksi di ~/.pg_service.conf dan password di ~/.pgpass (chmod 600).
# Simpan salinan terenkripsi di luar VPS dan uji restore sebelum menerima transaksi live.
backup_root="${BACKUP_DIRECTORY:-/var/backups/tapaklokal}"
storage_root="${PLATFORM_STORAGE_DIRECTORY:-/var/www/tapaklokal/shared/storage}"
test -d "$storage_root/app/private"
mkdir -p "$backup_root"
backup_run=$(mktemp -d "$backup_root/$(date -u +%Y%m%dT%H%M%SZ)-XXXXXX")
export PGSERVICE="${PGSERVICE:-tapaklokal}"
pg_dump --format=custom --file="$backup_run/database.dump"
pg_restore --list "$backup_run/database.dump" > "$backup_run/database-contents.txt"
tar --exclude='app/private/bootstrap' --exclude='app/private/database-backups' -czf "$backup_run/media.tar.gz" -C "$storage_root" app/private app/public
tar -tzf "$backup_run/media.tar.gz" > /dev/null
sha256sum "$backup_run/database.dump" "$backup_run/media.tar.gz" > "$backup_run/SHA256SUMS"
printf 'Backup lokal selesai: %s\n' "$backup_run"
# Tidak menghapus backup lama secara otomatis. Retensi dan replikasi ditentukan operator VPS.
