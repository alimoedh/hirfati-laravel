#!/bin/sh
set -e

# كتابة .env مباشرة بالقيم الثابتة
cat > /app/.env <<'EOF'
APP_NAME=Hirfati
APP_ENV=production
APP_KEY=base64:lceySTcheu2qyz2jp/QBSwWsKA7xU7hnPZSIOiLBNF8=
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Aden
APP_LOCALE=ar

LOG_CHANNEL=stderr
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=mysql.railway.internal
DB_PORT=3306
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=PUT_MYSQL_PASSWORD_HERE

SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
FILESYSTEM_DISK=public
EOF

# تفريغ الكاش
php artisan config:clear || true
php artisan cache:clear || true

# تشغيل السيرفر
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
