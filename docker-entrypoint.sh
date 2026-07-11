#!/bin/sh

# Create bootstrap/cache directory if missing
mkdir -p /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/bootstrap/cache

# Generate app key if not set
php artisan key:generate --no-interaction --force

# Run migrations
php artisan migrate --force

# Seed database (optional – comment out if not needed)
# php artisan db:seed --force

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Start PHP-FPM
php-fpm