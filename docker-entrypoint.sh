#!/bin/sh
set -e

echo "=== STARTING ENTRYPOINT ==="

# Create directories
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

echo "=== CHECKING COMPOSER ==="
composer --version || echo "Composer not found!"

echo "=== CHECKING VENDOR DIRECTORY ==="
if [ -d "vendor" ]; then
    echo "✅ Vendor directory exists"
    ls -la vendor/ | head -10
else
    echo "❌ Vendor directory NOT found - installing dependencies..."
    composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-req=ext-gd
fi

echo "=== CHECKING LARAVEL FRAMEWORK ==="
if [ -d "vendor/laravel/framework" ]; then
    echo "✅ Laravel framework installed!"
else
    echo "❌ Laravel framework NOT installed - forcing install..."
    composer require laravel/framework --no-update
    composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-req=ext-gd
fi

echo "=== RUNNING MIGRATIONS ==="
php artisan migrate --force || echo "Migrations failed, continuing..."

echo "=== RUNNING SEEDERS ==="
php artisan db:seed --force || echo "Seeders failed, continuing..."

echo "=== LINKING STORAGE ==="
php artisan storage:link || true

echo "=== CLEARING CACHES ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear

echo "=== STARTING SERVER ==="
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}