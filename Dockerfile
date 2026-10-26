FROM richarvey/nginx-php-fpm:3.1.6

# 1) إعادة بناء GD لدعم WebP
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

# نسخ ملفات المشروع إلى الحاوية
COPY . .

# السماح لـ Composer بالعمل كـ Root
ENV COMPOSER_ALLOW_SUPERUSER 1

# تثبيت الحزم مباشرة أثناء بناء الصورة لتوليد مجلد vendor وحل المشكلة نهائياً
RUN composer install --no-dev --optimize-autoloader --no-interaction

# إنشاء المجلدات المطلوبة للتخزين والصلاحيات
RUN mkdir -p \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/views \
        storage/app/public \
        storage/logs \
        bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

# إعدادات البيئة لـ Nginx و PHP-FPM
ENV WEBROOT /var/www/html/public
ENV PHP_ERRORS_STDERR 1
ENV RUN_SCRIPTS 1
ENV REAL_IP_HEADER 1
ENV SKIP_CHOWN 0
ENV APP_ENV production
ENV APP_DEBUG false
ENV LOG_CHANNEL stderr
ENV PHP_MEM_LIMIT 256
ENV PHP_POST_MAX_SIZE 12
ENV PHP_UPLOAD_MAX_FILESIZE 10

CMD ["/start.sh"]