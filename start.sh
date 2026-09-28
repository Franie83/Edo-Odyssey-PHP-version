#!/bin/bash
set -e

echo "=== Starting Laravel Application ==="

# ------------------------------------------------------------
# Create storage directories
# ------------------------------------------------------------
mkdir -p /var/www/html/storage/app/public
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
mkdir -p /var/www/html/bootstrap/cache

chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# ------------------------------------------------------------
# Parse DATABASE_URL
# ------------------------------------------------------------
if [ -n "$DATABASE_URL" ]; then
    echo "=== Parsing DATABASE_URL ==="
    DB_URL_NO_SCHEME="${DATABASE_URL#*://}"
    DB_CREDS="${DB_URL_NO_SCHEME%%@*}"
    DB_HOSTPART="${DB_URL_NO_SCHEME#*@}"
    export DB_USERNAME="${DB_CREDS%%:*}"
    export DB_PASSWORD="${DB_CREDS#*:}"
    DB_HOSTPORT="${DB_HOSTPART%%/*}"
    export DB_HOST="${DB_HOSTPORT%%:*}"
    export DB_PORT="${DB_HOSTPORT#*:}"
    export DB_DATABASE="${DB_HOSTPART#*/}"
    echo "    DB_HOST=$DB_HOST"
    echo "    DB_PORT=$DB_PORT"
    echo "    DB_DATABASE=$DB_DATABASE"
    echo "    DB_USERNAME=$DB_USERNAME"
fi

# ------------------------------------------------------------
# Write .env
# ------------------------------------------------------------
echo "=== Writing .env ==="
cat > /var/www/html/.env <<EOF
APP_NAME="Edo Odyssey"
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-https://edo-odyssey.onrender.com}

LOG_CHANNEL=${LOG_CHANNEL:-stderr}

DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
DATABASE_URL=${DATABASE_URL}

CACHE_STORE=file
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync

CLOUDINARY_CLOUD_NAME=${CLOUDINARY_CLOUD_NAME}
CLOUDINARY_API_KEY=${CLOUDINARY_API_KEY}
CLOUDINARY_API_SECRET=${CLOUDINARY_API_SECRET}
EOF

chown www-data:www-data /var/www/html/.env
chmod 644 /var/www/html/.env

# ------------------------------------------------------------
# Clear caches
# ------------------------------------------------------------
echo "=== Clearing caches ==="
php artisan config:clear
php artisan view:clear
php artisan route:clear
php artisan clear-compiled

# ------------------------------------------------------------
# Migrations
# ------------------------------------------------------------
echo "=== Running Migrations ==="
php artisan migrate --force --verbose

echo "=== Clearing application cache ==="
php artisan cache:clear || true

# ------------------------------------------------------------
# Seeding
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
# Storage link
# ------------------------------------------------------------
echo "=== Linking Storage ==="
php artisan storage:link || true
ln -sf /var/www/html/storage/app/public /var/www/html/public/storage || true
chown -R www-data:www-data /var/www/html/storage/app/public || true
chown -R www-data:www-data /var/www/html/public/storage || true

# ------------------------------------------------------------
# Optimize
# ------------------------------------------------------------
echo "=== Optimizing Application ==="
php artisan optimize

# ------------------------------------------------------------
# Set proper ownership for Nginx
# ------------------------------------------------------------
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# ------------------------------------------------------------
# Start Supervisor (Nginx + PHP-FPM)
# ------------------------------------------------------------
echo "=== Starting Nginx + PHP-FPM via Supervisor ==="
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
