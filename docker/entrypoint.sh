#!/bin/bash
set -e

cd /var/www/html

# Install dependencies if needed (first run)
if [ ! -d vendor ]; then
    composer install --no-dev --no-interaction
fi

# Generate and persist APP_KEY on first run
if [ ! -f .env ]; then
    echo "APP_KEY=" > .env
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force -n

# Create default admin user if none exists
php artisan solder:setup --no-interaction

# Build frontend assets if not already built (e.g. volume-mounted source)
if [ ! -d public/build ]; then
    if command -v node &> /dev/null; then
        npm install --no-audit --no-fund
        npm run build
    fi
fi

# Ensure the web server can write to storage and cache
chgrp -R www-data storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;

exec php-fpm
