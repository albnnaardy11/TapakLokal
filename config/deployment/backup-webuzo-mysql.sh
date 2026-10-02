#!/usr/bin/env bash
set -euo pipefail
umask 077
# Untuk MySQL/MariaDB Webuzo, jalankan sebagai user situs/backup.
# File kredensial client dibuat operator dengan mode 600, berisi [client] user/password/host.
# Tidak membaca .env ke output atau menaruh password di argumen proses.
: "${MYSQL_CREDENTIALS_FILE:?Set MYSQL_CREDENTIALS_FILE to the private client option file}"
: "${DATABASE_NAME:?Set DATABASE_NAME}"
: "${PLATFORM_STORAGE_DIRECTORY:?Set PLATFORM_STORAGE_DIRECTORY to shared storage}"
: "${BACKUP_DIRECTORY:?Set BACKUP_DIRECTORY outside public and release directories}"
test -r "$MYSQL_CREDENTIALS_FILE"
test -d "$PLATFORM_STORAGE_DIRECTORY/app/private"
mkdir -p "$BACKUP_DIRECTORY"
backup_run=$(mktemp -d "$BACKUP_DIRECTORY/$(date -u +%Y%m%dT%H%M%SZ)-XXXXXX")
# Gunakan mysqldump versi server yang dipasang Webuzo. Semua tabel aplikasi harus InnoDB.
mysqldump --defaults-extra-file="$MYSQL_CREDENTIALS_FILE" --single-transaction --quick --routines --events --hex-blob "$DATABASE_NAME" > "$backup_run/database.sql"
test -s "$backup_run/database.sql"
gzip "$backup_run/database.sql"
gzip -t "$backup_run/database.sql.gz"
tar --exclude='app/private/bootstrap' --exclude='app/private/database-backups' -czf "$backup_run/media.tar.gz" -C "$PLATFORM_STORAGE_DIRECTORY" app/private app/public
tar -tzf "$backup_run/media.tar.gz" > /dev/null
sha256sum "$backup_run/database.sql.gz" "$backup_run/media.tar.gz" > "$backup_run/SHA256SUMS"
printf 'Backup lokal selesai: %s\n' "$backup_run"
# Replikasi terenkripsi ke tujuan di luar VPS dan uji restore diperlukan sebelum go-live.
# Script tidak menghapus backup lama atau menganggap backup lokal sebagai disaster recovery.
