#!/bin/sh
set -e

echo "=== STARTING ENTRYPOINT ==="

# Create directories
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache
mkdir -p public/storage

# Set permissions
chmod -R 775 storage bootstrap/cache

echo "=== CHECKING COMPOSER ==="
composer --version || echo "Composer not found!"

echo "=== CHECKING VENDOR DIRECTORY ==="
if [ -d "vendor" ]; then
    echo "✅ Vendor directory exists"
else
    echo "❌ Vendor directory NOT found - installing dependencies..."
    composer install --no-interaction --optimize-autoloader --no-dev
fi

echo "=== CHECKING LARAVEL FRAMEWORK ==="
if [ -d "vendor/laravel/framework" ]; then
    echo "✅ Laravel framework installed!"
else
    echo "❌ Laravel framework NOT installed - forcing install..."
    composer install --no-interaction --optimize-autoloader --no-dev
fi

echo "=== GENERATING APP KEY ==="
php artisan key:generate --no-interaction --force || echo "Key generation skipped"

echo "=== RUNNING MIGRATIONS ==="
php artisan migrate --force || echo "Migrations failed, continuing..."

echo "=== LINKING STORAGE ==="
php artisan storage:link || true

echo "=== CLEARING CACHES ==="
php artisan config:clear || true
php artisan cache:clear || true
php artisan view:clear || true
php artisan route:clear || true

echo "=== STARTING SERVER ==="
echo "Using PORT: ${PORT:-10000}"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-10000}