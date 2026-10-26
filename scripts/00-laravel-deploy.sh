#!/usr/bin/env bash
#
# Hermes/Herafy deploy script.
#
# richarvey/nginx-php-fpm runs this on EVERY container start (ENV RUN_SCRIPTS 1),
# in name order, from ${SCRIPTS_DIR:-/var/www/html/scripts}.
# Render therefore applies caches and migrations on each deploy AND on each
# cold start after the free instance has been spun down.
#
set -euo pipefail

cd /var/www/html

echo "==> Installing composer dependencies (no-dev)"
composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader \
    --working-dir=/var/www/html

echo "==> Ensuring writable directories exist"
mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    storage/app/public \
    storage/logs \
    bootstrap/cache

echo "==> Creating the public/storage symlink"
# The upload code writes through public_path('storage/...'), which only resolves
# when public/storage -> storage/app/public exists.
php artisan storage:link --force

echo "==> Caching configuration"
php artisan config:cache

echo "==> Caching routes"
php artisan route:cache

echo "==> Caching Blade views"
php artisan view:cache

echo "==> Running database migrations"
php artisan migrate --force

echo "==> Handing storage/ and bootstrap/cache/ back to the php-fpm user"
chown -R nginx:nginx storage bootstrap/cache

echo "==> Deploy script finished"
