FROM node:26.13.1-slim@sha256:e6bedef217677b276c591795e169840edd9096dbf13f29df8ef8d1856b8820bc AS assets

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

COPY --from=composer:2.11.1@sha256:8de2184c6de224f4689e8f80ed5f5edaf0030b6b8b7839a22a2bcb6722ac87d0 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY --from=assets /app/public/build public/build
