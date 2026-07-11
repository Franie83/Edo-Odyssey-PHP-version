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

# Run migrations
php artisan migrate --force

# Run seeders
php artisan db:seed --force

# Link storage
php artisan storage:link || true

# Clear caches (don't cache during debugging)
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Start server
php artisan serve --host=0.0.0.0 --port=${PORT:-10000}