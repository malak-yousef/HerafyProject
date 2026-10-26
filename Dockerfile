FROM richarvey/nginx-php-fpm:3.1.6

# -----------------------------------------------------------------------------
# 1) Rebuild GD with WebP support
#    The upstream image compiles GD WITHOUT --with-webp, so imagewebp() is
#    missing. app/Services/ImageOptimizerService.php calls ->toWebp() for any
#    uploaded .webp file (validation allows webp everywhere) outside its
#    try/catch, which would raise a 500. Rebuild GD with webp to match the
#    local XAMPP behaviour.
# -----------------------------------------------------------------------------
RUN apk add --no-cache --virtual .gd-build \
        autoconf make gcc musl-dev linux-headers \
        libwebp-dev libpng-dev freetype-dev libjpeg-turbo-dev && \
    docker-php-ext-configure gd \
        --enable-gd \
        --with-freetype \
        --with-jpeg \
        --with-webp && \
    docker-php-ext-install gd && \
    apk del .gd-build && \
    apk add --no-cache libwebp

# Copy the application into the image's WORKDIR (/var/www/html)
COPY . .

# -----------------------------------------------------------------------------
# 2) Runtime directories that git does not track (empty dirs are not committed)
# -----------------------------------------------------------------------------
RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/app/public \
        storage/logs \
        bootstrap/cache

# -----------------------------------------------------------------------------
# 3) richarvey/nginx-php-fpm image switches (read by /start.sh)
# -----------------------------------------------------------------------------
# Skip the image's own composer install; scripts/00-laravel-deploy.sh does it.
ENV SKIP_COMPOSER 1

# Laravel's public directory becomes the nginx document root.
ENV WEBROOT /var/www/html/public

# Send PHP errors to stderr so Render's log stream captures them.
ENV PHP_ERRORS_STDERR 1

# Execute /var/www/html/scripts/* in name order on every container start.
ENV RUN_SCRIPTS 1

# Honour X-Forwarded-For from Render's TLS-terminating proxy.
ENV REAL_IP_HEADER 1

# PHP-FPM runs as the "nginx" user; let it write to storage/ and bootstrap/cache/.
ENV SKIP_CHOWN 0

# -----------------------------------------------------------------------------
# 4) Laravel production settings (override per-environment in the Render UI)
# -----------------------------------------------------------------------------
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr

# Defaults sized for this app (validation caps uploads at 2-4 MB).
ENV PHP_MEM_LIMIT 256
ENV PHP_POST_MAX_SIZE 12
ENV PHP_UPLOAD_MAX_FILESIZE 10

# Allow composer to run as root inside the build/start phase.
ENV COMPOSER_ALLOW_SUPERUSER 1

CMD ["/start.sh"]
