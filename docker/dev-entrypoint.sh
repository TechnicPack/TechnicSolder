#!/bin/bash
set -e

cd /var/www/html

composer install --no-interaction

# Generate .env for development if it doesn't exist
if [ ! -f .env ]; then
    cp .env.example .env
    sed -i 's/^APP_ENV=.*/APP_ENV=local/' .env
    sed -i 's/^APP_DEBUG=.*/APP_DEBUG=true/' .env
    sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=pgsql/' .env
    sed -i 's/^DB_HOST=.*/DB_HOST=postgres/' .env
    sed -i 's/^DB_PORT=.*/DB_PORT=5432/' .env
    sed -i 's/^DB_DATABASE=.*/DB_DATABASE=solder/' .env
    sed -i 's/^DB_USERNAME=.*/DB_USERNAME=solder/' .env
    sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=solder/' .env
    sed -i 's/^REDIS_HOST=.*/REDIS_HOST=redis/' .env
    sed -i 's/^CACHE_STORE=.*/CACHE_STORE=redis/' .env
    sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=redis/' .env
    php artisan key:generate --force
fi

php artisan migrate --force

# Build frontend assets if not already built
if [ ! -d public/build ]; then
    if command -v node &> /dev/null; then
        npm install --no-audit --no-fund
        npm run build
    fi
fi

# Create storage directories if needed
mkdir -p storage/mods
chgrp -R www-data storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

php-fpm
