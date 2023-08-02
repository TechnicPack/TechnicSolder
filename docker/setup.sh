# Copy the default .env file into the container
# and root application folder.
cp docker/.env .env
composer install --no-dev --no-interaction

# Setup for Solder
# Generate a new key and migrate the database
# It's best not to run this more than once
php artisan key:generate --force -n
php artisan migrate --force -n

# Find all directories and files below the current directory
# and set their permissions to something secure and sane
chown -R www-data:www-data .
find . -type d -print0 | xargs -0 chmod 755
find . -type f -print0 | xargs -0 chmod 644