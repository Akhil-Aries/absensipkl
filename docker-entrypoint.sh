#!/bin/bash
set -e

# Sesuaikan port Apache dengan PORT dinamis dari Render
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/ports.conf /etc/apache2/sites-available/*.conf
fi

# Buat symlink storage jika belum ada
php artisan storage:link || true

# Jalankan migrasi database otomatis
php artisan migrate --force || true

# Jalankan seeder admin jika tabel users kosong
php artisan db:seed --force || true

# Cache konfigurasi untuk performa optimal di production
php artisan config:cache || true
php artisan route:cache || true
php artisan view:cache || true

# Jalankan Apache web server
exec apache2-foreground
