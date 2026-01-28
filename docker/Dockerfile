FROM php:8.5-fpm

RUN apt-get update && apt-get install -y unzip nginx \
    && docker-php-ext-install bcmath pdo_mysql \
    && pecl install redis-6.3.0 \
    && docker-php-ext-enable redis
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

