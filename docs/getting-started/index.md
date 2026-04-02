# Bare-Metal Installation

!!! tip "Docker is the recommended approach"
    The fastest way to get Solder running is with Docker. See the [Docker setup guide](docker.md) for a streamlined installation that handles dependencies, migrations, and initial setup automatically.

This guide walks through installing Technic Solder directly on a Linux server.

!!! warning
    Solder must be installed on a server that is always running and online. If your Solder instance is not reachable, your modpack will not be available in the Technic Launcher.

!!! note
    Running Solder on Windows is not supported and no help will be provided.

## Requirements

- **Git**
- **PHP >= 8.4**
- **Composer** ([getcomposer.org](https://getcomposer.org/))
- **unzip**
- **A MariaDB, MySQL, or PostgreSQL database**

### Required PHP Extensions

BCMath, cURL, ctype, fileinfo, JSON, mbstring, OpenSSL, PDO, Redis, Tokenizer, XML, ZIP

### Example: Ubuntu 24.04

```bash
# Add the PHP PPA: https://launchpad.net/~ondrej/+archive/ubuntu/php
LC_ALL=C.UTF-8 sudo add-apt-repository ppa:ondrej/php
sudo apt update

# If you're using PostgreSQL, replace php8.4-mysql with php8.4-pgsql
sudo apt install git unzip php8.4 php8.4-bcmath php8.4-cli php8.4-curl \
    php8.4-fpm php8.4-mbstring php8.4-mysql php8.4-redis php8.4-xml php8.4-zip
```

## Installing Composer

Solder uses Composer to manage PHP dependencies. Install it using [the official guide](https://getcomposer.org/doc/00-intro.md#installation-linux-unix-macos). Installing Composer globally is recommended.

If you already have Composer installed, make sure it is up to date:

```bash
# Globally installed
sudo composer self-update

# Locally installed
php composer.phar self-update
```

The rest of this guide assumes a global Composer installation. Adjust commands accordingly if you installed it locally.

## Installing Solder

Clone the repository and install dependencies:

```bash
git clone https://github.com/TechnicPack/TechnicSolder.git
cd TechnicSolder
composer install --no-dev --no-interaction
```

!!! note
    If you want to help develop Solder, omit `--no-dev` to include development dependencies.

## Configuring Solder

Solder is configured through a `.env` file. Create one from the example:

```bash
cp .env.example .env
```

Open `.env` in your text editor and update the variables listed below. Leave any variables not in this table at their default values.

| Variable | Description |
|----------|-------------|
| `APP_URL` | The URL where Solder will be accessible (e.g. `https://solder.example.com`). |
| `DB_CONNECTION` | Database type: `mariadb`, `mysql`, or `pgsql`. |
| `DB_HOST` | Database server IP. Use `127.0.0.1` if running on the same server. |
| `DB_PORT` | Database port. `3306` for MySQL/MariaDB, `5432` for PostgreSQL. |
| `DB_DATABASE` | Name of your database. |
| `DB_USERNAME` | Database username. |
| `DB_PASSWORD` | Database password. |
| `CACHE_STORE` | Cache backend. Use `file` for local storage or `redis` (recommended) if you have a Redis server. |
| `SESSION_DRIVER` | Session backend. Same options as `CACHE_STORE`. |
| `REDIS_HOST` | Redis server IP (if using Redis). |
| `REDIS_PASSWORD` | Redis password, if one is set. |
| `REDIS_PORT` | Redis server port (default `6379`). |
| `SOLDER_REPO_LOCATION` | Path or URL to your mod repository (see below). |
| `SOLDER_MIRROR_URL` | Web-accessible URL to your mod repository (see below). |
| `SOLDER_MD5_CONNECT_TIMEOUT` | Seconds to wait before giving up on a remote MD5 checksum calculation. |

### Repository location and mirror URL

- **`SOLDER_REPO_LOCATION`** points to where your mod files are stored. If Solder is on the same server as your mod repository, use the absolute filesystem path (e.g. `/var/www/mods/`). This is faster because MD5 checksums are calculated locally. If your mods are hosted elsewhere, use the web-accessible URL instead. A trailing slash is recommended but will be added automatically if omitted.

- **`SOLDER_MIRROR_URL`** is the web-accessible URL that players' launchers will use to download mods (e.g. `https://mods.example.com/`). If you used a URL for the repo location, you can use the same value here. A trailing slash is recommended but will be added automatically if omitted.

## Generate Application Key

Solder requires an encryption key to function. Generate one automatically:

```bash
php artisan key:generate
```

This sets the `APP_KEY` value in your `.env` file.

## Database Setup

Create a database for Solder. The default `DB_DATABASE` in `.env.example` is `laravel` — you'll likely want to change this to something like `solder`. Use any name as long as it matches `DB_DATABASE` in your `.env`.

Instructions for creating the database vary by database server and are not covered here.

Once your database is ready, run the migration to create all required tables:

```bash
php artisan migrate --force
```

!!! note
    The `--force` flag is needed because Solder runs in production mode by default. It is safe to use here.

## Initial Setup

After migrating, run the setup command to create the default admin user:

```bash
php artisan solder:setup
```

In non-interactive mode (e.g. scripts), this creates an admin with the default credentials. You can also specify credentials directly:

```bash
php artisan solder:setup --email=admin@example.com --password=your-secure-password
```

## Web Server Configuration

Your web server (nginx or Apache) needs to be configured to serve Solder's `public/` directory and pass PHP requests to PHP-FPM.

See [Example Web Server Configs](../guides/web-server-configs.md) for complete nginx and Apache configuration examples.

### File permissions

Your web server user needs:

- **Read** access to the entire Solder directory
- **Read and write** access to `storage/` and `bootstrap/cache/`

```bash
# Example: set ownership to the www-data user
sudo chown -R www-data:www-data /path/to/TechnicSolder/storage
sudo chown -R www-data:www-data /path/to/TechnicSolder/bootstrap/cache
```

### HTTPS

If you use HTTPS (recommended), make sure `APP_URL` in your `.env` starts with `https://`.

!!! warning
    If Solder is behind a load balancer or reverse proxy that terminates TLS (e.g. Cloudflare), you may need to configure your PHP-FPM pool with the `HTTPS` environment variable. See the [web server configs guide](../guides/web-server-configs.md) for details.

## Optimize

Cache Solder's configuration, routes, and views for better performance:

```bash
php artisan optimize
```

## Default Credentials

Log in at your configured `APP_URL` with:

- **Email:** `admin@admin.com`
- **Password:** `admin`

!!! warning
    Change these credentials immediately after your first login.

## Keeping Solder Updated

Solder's dependencies are frequently updated. Set up a cron job (or similar) to keep them current:

```bash
cd /path/to/TechnicSolder && composer update --no-dev --no-interaction && php artisan optimize
```

!!! tip
    You can also pull the latest Solder code at the same time by adding `git pull` before `composer update`.
