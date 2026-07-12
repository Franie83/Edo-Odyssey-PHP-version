#!/bin/bash

echo "=== Starting Laravel Application ==="

# Create storage directories if they don't exist
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

# Set permissions
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Clear all caches aggressively
echo "=== Clearing all caches ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled

# Generate app key if not set
echo "=== Generating App Key ==="
php artisan key:generate --no-interaction --force

# Run migrations fresh (drops all tables and recreates)
echo "=== Running Fresh Migrations ==="
php artisan migrate:fresh --force --verbose

# Run seeders
echo "=== Running Seeders ==="
php artisan db:seed --force --verbose

# Link storage
echo "=== Linking Storage ==="
php artisan storage:link || true

# Create storage symlink for public access (alternative method)
echo "=== Creating storage symlink ==="
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage || true

# Set proper permissions for uploaded files
echo "=== Setting storage permissions ==="
chmod -R 775 /var/www/html/storage/app/public
chmod -R 775 /var/www/html/public/storage

# Optimize for production
echo "=== Optimizing Application ==="
php artisan optimize

# Restart the server
echo "=== Starting Server ==="
php artisan serve --host=0.0.0.0 --port=10000