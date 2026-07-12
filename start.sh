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

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Generate app key if not set
echo "=== Generating App Key ==="
php artisan key:generate --no-interaction --force

# Run migrations with verbose output
echo "=== Running Migrations ==="
php artisan migrate --force --verbose

# Link storage
echo "=== Linking Storage ==="
php artisan storage:link || true

# Start the server
echo "=== Starting Server ==="
php artisan serve --host=0.0.0.0 --port=10000