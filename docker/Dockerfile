FROM node:26.12.0-slim@sha256:e556a144463cc8dc53afe8de6d2fa0db5c132fd39ffa232c17641bd40df2bf11 AS assets

WORKDIR /app
COPY package.json package-lock.json .npmrc ./
RUN npm ci
COPY vite.config.js ./
COPY resources/ resources/
RUN npm run build

FROM php:8.5-fpm@sha256:f665ad19c093eee54db3a03c89102d6828b1bbf4f327689e5947a8686c6585a1

RUN apt-get update && apt-get install -y \
        unzip \
        libzip-dev \
        libpq-dev \
    && docker-php-ext-install bcmath pdo_mysql pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/* \
    && echo 'memory_limit = 256M' > /usr/local/etc/php/conf.d/memory.ini

COPY --from=composer:2.10.3@sha256:af98f42dfff7c68ba8d53c2164fd9fde1087b7d449514baa38c418b1f6bc4bac /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY --from=assets /app/public/build public/build
