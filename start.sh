#!/bin/bash
set -e

echo "=== Starting Laravel Application ==="

# ------------------------------------------------------------
# Create storage directories if they don't exist
# ------------------------------------------------------------
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

# Set permissions
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# ------------------------------------------------------------
# Write .env from Render environment variables
# ------------------------------------------------------------
echo "=== Writing .env from Render env vars ==="
cat > /var/www/html/.env <<EOF
APP_NAME="Edo Odyssey"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://edo-odyssey.onrender.com}

LOG_CHANNEL=${LOG_CHANNEL:-stderr}

DB_CONNECTION=pgsql
DATABASE_URL=${DATABASE_URL}

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

CLOUDINARY_CLOUD_NAME=${CLOUDINARY_CLOUD_NAME}
CLOUDINARY_API_KEY=${CLOUDINARY_API_KEY}
CLOUDINARY_API_SECRET=${CLOUDINARY_API_SECRET}
EOF

chmod 644 /var/www/html/.env

# ------------------------------------------------------------
# Clear all caches
# ------------------------------------------------------------
echo "=== Clearing all caches ==="
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled

# ------------------------------------------------------------
# Run migrations (NOT migrate:fresh — preserves data)
# ------------------------------------------------------------
echo "=== Running Migrations ==="
php artisan migrate --force --verbose

# ------------------------------------------------------------
# Seed only if database is empty
# ------------------------------------------------------------
echo "=== Checking if database needs seeding ==="
USER_COUNT=$(php artisan tinker --execute="echo \App\Models\User::count();" 2>/dev/null || echo "0")
if [ "$USER_COUNT" = "0" ]; then
    echo "=== Database is empty — running seeders ==="
    php artisan db:seed --force --verbose || echo "WARNING: Seeder failed, continuing anyway"
else
    echo "=== Database has $USER_COUNT users — skipping seeders ==="
fi

# ------------------------------------------------------------
# Storage links
# ------------------------------------------------------------
echo "=== Linking Storage ==="
php artisan storage:link || true
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage || true

# ------------------------------------------------------------
# Set permissions for uploaded files
# ------------------------------------------------------------
echo "=== Setting storage permissions ==="
chmod -R 775 /var/www/html/storage/app/public || true
chmod -R 775 /var/www/html/public/storage || true

# ------------------------------------------------------------
# Optimize for production
# ------------------------------------------------------------
echo "=== Optimizing Application ==="
php artisan optimize

# ------------------------------------------------------------
# Start server
# ------------------------------------------------------------
echo "=== Starting Server ==="
exec php artisan serve --host=0.0.0.0 --port=10000
