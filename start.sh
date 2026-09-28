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

chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# ------------------------------------------------------------
# Parse DATABASE_URL into DB_* variables (Laravel doesn't do this natively)
# ------------------------------------------------------------
if [ -n "$DATABASE_URL" ]; then
    echo "=== Parsing DATABASE_URL ==="
    # Strip scheme
    DB_URL_NO_SCHEME="${DATABASE_URL#*://}"
    # Split user:pass and host
    DB_CREDS="${DB_URL_NO_SCHEME%%@*}"
    DB_HOSTPART="${DB_URL_NO_SCHEME#*@}"
    # Extract user and password
    export DB_USERNAME="${DB_CREDS%%:*}"
    export DB_PASSWORD="${DB_CREDS#*:}"
    # Extract host and port
    DB_HOSTPORT="${DB_HOSTPART%%/*}"
    export DB_HOST="${DB_HOSTPORT%%:*}"
    export DB_PORT="${DB_HOSTPORT#*:}"
    # Extract database name
    export DB_DATABASE="${DB_HOSTPART#*/}"
    echo "    DB_HOST=$DB_HOST"
    echo "    DB_PORT=$DB_PORT"
    echo "    DB_DATABASE=$DB_DATABASE"
    echo "    DB_USERNAME=$DB_USERNAME"
fi

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

# --- Database ---
DB_CONNECTION=${DB_CONNECTION:-pgsql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-5432}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}
DATABASE_URL=${DATABASE_URL}

# --- Cache/Session/Queue (file-based for Render free tier) ---
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync

# --- Cloudinary ---
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
# Run migrations
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

chmod -R 775 /var/www/html/storage/app/public || true
chmod -R 775 /var/www/html/public/storage || true

# ------------------------------------------------------------
# Optimize
# ------------------------------------------------------------
echo "=== Optimizing Application ==="
php artisan optimize

# ------------------------------------------------------------
# Start server
# ------------------------------------------------------------
echo "=== Starting Server ==="
exec php artisan serve --host=0.0.0.0 --port=10000
