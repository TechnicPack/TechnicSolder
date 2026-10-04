FROM node:24.20.0-slim@sha256:ba849c60be29959425b8734d57b8b4b7d56f98edd9504c9af091d5281095a71e AS assets

WORKDIR /app
COPY package.json ./
RUN npm install
COPY vite.config.js ./
COPY resources/ resources/
RUN npm run build

FROM php:8.5-fpm@sha256:584e584083bada479f0ee733a3025722a68e366eb721e618d1b6a252e4ff45c5

RUN apt-get update && apt-get install -y \
        unzip \
        libzip-dev \
        libpq-dev \
    && docker-php-ext-install bcmath pdo_mysql pdo_pgsql zip \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/* \
    && echo 'memory_limit = 256M' > /usr/local/etc/php/conf.d/memory.ini

COPY --from=composer:2.10.3@sha256:a5f59b9fd2faf31218632be4809dc6491761085e8064c31dc3b84378c48c248b /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY --from=assets /app/public/build public/build
