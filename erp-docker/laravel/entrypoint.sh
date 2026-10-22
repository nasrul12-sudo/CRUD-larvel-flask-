#!/bin/sh
set -e

echo "🚀 ERP Laravel Entrypoint starting..."

# Tunggu folder public ada (artinya bind-mount sudah aktif)
if [ ! -f "/var/www/html/artisan" ]; then
    echo "⏳ Waiting for Laravel files to be mounted..."
    sleep 3
fi

cd /var/www/html

# Pastikan folder storage & bootstrap/cache ada
mkdir -p storage/framework/{sessions,views,cache} \
         storage/logs \
         bootstrap/cache

# Set permission (www-data UID 82 di Alpine)
chown -R www-data:www-data storage bootstrap/cache 2>/dev/null || true

# Install composer deps jika vendor belum ada
if [ ! -d "vendor" ]; then
    echo "📦 Installing composer dependencies..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Generate APP_KEY jika kosong
if ! grep -q "^APP_KEY=base64:" .env 2>/dev/null; then
    echo "🔑 Generating APP_KEY..."
    php artisan key:generate --force || true
fi

# Clear cache agar env baru terbaca
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

echo "✅ Laravel ready. Starting PHP-FPM..."

# Jalankan command yang diberikan (default: php-fpm)
exec "$@"
