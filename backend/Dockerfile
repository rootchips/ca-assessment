FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    git \
    unzip \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    mariadb-client \
    linux-headers \
    $PHPIZE_DEPS

RUN docker-php-ext-install pdo_mysql bcmath pcntl zip exif
RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

EXPOSE 8000 8080
