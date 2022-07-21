FROM php:8.1.8-fpm-alpine

RUN apk update && apk add \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql exif pcntl bcmath gd

RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
        && pecl install redis \
        && docker-php-ext-enable redis.so

COPY --from=composer:2.3.9 /usr/bin/composer /usr/local/bin/composer
