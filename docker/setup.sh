# Copy the default .env file into the container
# and root application folder.
cp docker/.env .env
composer install --no-dev --no-interaction

# Setup for Solder
# Generate a new key and migrate the database
# It's best not to run this more than once
php artisan key:generate -n
php artisan migrate --force -n

# Find all directories and files below the current directory
# and set their permissions to something secure and sane.
# We ignore the docker folder so it doesn't break permissions
# on the other containers.
echo "Fixing file permissions, this may take a minute..."
find . -path ./docker -prune -o -print0 | xargs -0 chown www-data:www-data
find . -path ./docker -prune -o -type d -print0 | xargs -0 chmod 755
find . -path ./docker -prune -o -type f -print0 | xargs -0 chmod 644
