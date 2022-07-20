FROM php:8.1.8-fpm-alpine

RUN apk update && apk add \
    git \
    curl \
    libpng-dev \
    libxml2-dev \
    zip \
    unzip

RUN docker-php-ext-install pdo_mysql exif pcntl bcmath gd

COPY --from=composer:2.3.9 /usr/bin/composer /usr/local/bin/composer
