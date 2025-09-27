
FROM dunglas/frankenphp:1.2-php8.3-alpine
RUN docker-php-ext-install pcntl
RUN apk add --no-cache bash git curl zip unzip icu-dev libzip-dev oniguruma-dev g++ make autoconf
RUN docker-php-ext-install pdo pdo_mysql intl zip
WORKDIR /var/www/html
EXPOSE 8080
CMD ["php", "-S", "0.0.0.0:8080", "-t", "public"]
