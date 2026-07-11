#!/bin/sh
set -e

echo "=== STARTING ENTRYPOINT ===" > /var/log/entrypoint.log
exec 1>/dev/stdout 2>/dev/stderr

# Create directories
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

echo "=== CHECKING PHP ==="
php -v

echo "=== CHECKING COMPOSER ==="
composer --version

echo "=== INSTALLING DEPENDENCIES ==="
composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-req=ext-gd -vvv

echo "=== CHECKING VENDOR ==="
if [ -d "vendor/laravel/framework" ]; then
    echo "✅ Laravel framework installed!"
else
    echo "❌ Laravel framework NOT installed!"
    ls -la vendor/ || echo "No vendor directory"
    exit 1
fi

echo "=== RUNNING MIGRATIONS ==="
php artisan migrate --force

echo "=== RUNNING SEEDERS ==="
php artisan db:seed --force

echo "=== LINKING STORAGE ==="
php artisan storage:link || true

echo "=== CLEARING CACHES ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "=== STARTING SERVER ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}