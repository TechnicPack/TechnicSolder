FROM node:24.14.1-slim AS assets

WORKDIR /app
COPY package.json ./
RUN npm install
COPY vite.config.js ./
COPY resources/ resources/
RUN npm run build

FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
        unzip \
        libzip-dev \
        libpq-dev \
    && docker-php-ext-install bcmath pdo_mysql pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/* \
    && echo 'memory_limit = 256M' > /usr/local/etc/php/conf.d/memory.ini

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY --from=assets /app/public/build public/build
