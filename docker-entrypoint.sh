#!/bin/sh
set -e

# Create directories
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p storage/logs
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage bootstrap/cache

# Remove vendor directory to force fresh install
rm -rf vendor

# Install dependencies
echo "Installing Composer dependencies..."
composer install --no-interaction --optimize-autoloader --no-dev --ignore-platform-req=ext-gd

# Run migrations
echo "Running migrations..."
php artisan migrate --force

# Run seeders
echo "Running seeders..."
php artisan db:seed --force

# Link storage
php artisan storage:link || true

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Start server
echo "Starting server..."
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}