FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git zip unzip libzip-dev libonig-dev libxml2-dev \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libicu-dev libssl-dev cron curl gnupg \
 && docker-php-ext-configure intl \
 && docker-php-ext-install pdo pdo_mysql zip intl

# Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
 && apt-get install -y nodejs

WORKDIR /var/www/html

COPY composer.json composer.lock* ./
RUN composer install --no-interaction --prefer-dist --no-scripts || true

# بقية المشروع
COPY . .

# COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN mkdir -p /var/www/html/storage /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
EXPOSE 9000
CMD ["php-fpm"]
