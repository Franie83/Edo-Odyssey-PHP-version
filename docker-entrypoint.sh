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
which composer || echo "Composer not found"
composer --version || echo "Composer version check failed"

echo "=== CHECKING VENDOR DIRECTORY ==="
if [ -d "vendor" ]; then
    echo "Vendor directory exists"
    ls -la vendor/ | head -20
else
    echo "Vendor directory NOT found"
fi

echo "=== CHECKING COMPOSER.JSON ==="
cat composer.json | grep "laravel/framework" || echo "laravel/framework not found in composer.json"

echo "=== REMOVING VENDOR ==="
rm -rf vendor
rm -f composer.lock

echo "=== INSTALLING DEPENDENCIES ==="
composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-req=ext-gd -vvv

echo "=== CHECKING VENDOR AFTER INSTALL ==="
if [ -d "vendor/laravel/framework" ]; then
    echo "✅ Laravel framework installed successfully!"
    ls -la vendor/laravel/framework/
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