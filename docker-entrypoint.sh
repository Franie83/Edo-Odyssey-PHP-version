#!/bin/sh

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

php-fpm