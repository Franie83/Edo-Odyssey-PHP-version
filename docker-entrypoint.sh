#!/bin/sh

# Create all required directories
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache
mkdir -p /var/www/html/public/storage

# Set permissions
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# Create .env if it doesn't exist
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

# Generate app key (only if not set)
php artisan key:generate --no-interaction --force

# Run migrations
php artisan migrate --force

# Clear and cache config, routes, views
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Link storage
php artisan storage:link || true

# Start PHP built-in server
php -S 0.0.0.0:${PORT:-10000} -t public