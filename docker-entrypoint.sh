#!/bin/sh
set -e

# إنشاء .env من متغيرات Railway
cat > /app/.env <<EOF
APP_NAME="${APP_NAME:-حرفتي}"
APP_ENV="${APP_ENV:-production}"
APP_KEY="${APP_KEY}"
APP_DEBUG="${APP_DEBUG:-false}"
APP_URL="${APP_URL:-http://localhost}"

DB_CONNECTION="${DB_CONNECTION:-mysql}"
DB_HOST="${DB_HOST}"
DB_PORT="${DB_PORT}"
DB_DATABASE="${DB_DATABASE}"
DB_USERNAME="${DB_USERNAME}"
DB_PASSWORD="${DB_PASSWORD}"

SESSION_DRIVER="${SESSION_DRIVER:-file}"
CACHE_STORE="${CACHE_STORE:-file}"
QUEUE_CONNECTION="${QUEUE_CONNECTION:-sync}"
FILESYSTEM_DISK="${FILESYSTEM_DISK:-public}"

LOG_CHANNEL="${LOG_CHANNEL:-stderr}"
EOF

# تفريغ الكاش
php artisan config:clear || true
php artisan cache:clear || true

# تشغيل السيرفر
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
