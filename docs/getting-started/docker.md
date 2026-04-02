# Docker Setup

Docker is the recommended way to run Technic Solder. It packages the application and all its dependencies into containers, so there is nothing to install on the host beyond Docker itself.

## Prerequisites

- **Docker Engine** 20.10+ ([install guide](https://docs.docker.com/engine/install/))
- **Docker Compose** v2 (included with Docker Desktop; on Linux, install the [Compose plugin](https://docs.docker.com/compose/install/linux/))
- **Git**

## Quick Start

```bash
git clone https://github.com/TechnicPack/TechnicSolder.git
cd TechnicSolder
docker compose up -d
```

That's it. The first boot takes a minute or two while the image builds and the setup script runs. Once the database passes its health check and the other containers are running, Solder is ready at [http://localhost](http://localhost).

!!! tip "What happens on first boot"
    The entrypoint script (`docker/entrypoint.sh`) runs automatically and handles everything:

    1. Installs PHP dependencies via Composer
    2. Generates an `APP_KEY` if no `.env` file exists
    3. Runs database migrations
    4. Creates the initial admin user
    5. Builds frontend assets if needed
    6. Sets file permissions
    7. Starts PHP-FPM

    Subsequent boots skip the steps that have already been completed, so restarts are fast.

## Initial Admin User

On first boot, an admin user is created with email `admin@admin.com` and a randomly generated password printed to the console. Check the container logs to find it:

```bash
docker compose logs solder | grep "Generated password"
```

To set specific credentials, add these to your `.env` file before the first boot:

```bash
SOLDER_INITIAL_ADMIN_EMAIL=admin@example.com
SOLDER_INITIAL_ADMIN_PASSWORD=your-secure-password
```

!!! warning
    Change these credentials immediately after your first login.

## Configuration

Configuration is done through environment variables. You have two options:

1. **Create a `.env` file** in the project root (recommended for most settings)
2. **Override individual variables** directly in `compose.yml` under the `solder` service's `environment` section

The `.env` file is loaded automatically by Docker Compose. Any variables you set there will be substituted into the `compose.yml` environment block.

### Key Variables

| Variable | Default | Description |
|----------|---------|-------------|
| `SOLDER_PORT` | `80` | Host port for the nginx container |
| `APP_URL` | `http://localhost` | Public URL where Solder is accessible |
| `APP_TIMEZONE` | `UTC` | Application timezone |
| `DB_DATABASE` | `solder` | MariaDB database name |
| `DB_USERNAME` | `solder` | MariaDB username |
| `DB_PASSWORD` | `solder` | MariaDB password |
| `SOLDER_REPO_LOCATION` | `/var/www/mods.solder.test/` | Path or URL to your mod repository |
| `SOLDER_MIRROR_URL` | `http://mods.solder.test/` | Public URL for mod downloads |
| `SOLDER_CORS_ORIGINS` | `*` | Allowed CORS origins |
| `MAIL_ENABLED` | `false` | Enable email functionality |

!!! note
    For a complete list of all environment variables and what they do, see the [Configuration reference](configuration.md).

### Example `.env` File

```bash
SOLDER_PORT=8080
APP_URL=https://solder.example.com
DB_PASSWORD=a-strong-database-password
SOLDER_MIRROR_URL=https://mods.example.com/
SOLDER_REPO_LOCATION=https://mods.example.com/
```

After changing environment variables, restart the stack to apply them:

```bash
docker compose up -d
```

## Architecture

The Docker stack consists of four containers:

| Container | Image | Role |
|-----------|-------|------|
| **nginx** | `nginx` | Reverse proxy, serves static files, forwards PHP requests to the app |
| **solder** | Built from `docker/Dockerfile` | PHP-FPM application server |
| **mysql** | `mariadb` | MariaDB database with a health check |
| **redis** | `valkey/valkey` | Valkey (Redis-compatible) for caching, sessions, and queues |

The solder container depends on mysql (waits for its health check to pass) and redis (waits for it to start). The nginx container depends on solder. This ensures everything starts in the correct order.

### Data Persistence

- **Database files** are stored in `./docker/mysql/` on the host
- **Application files** are bind-mounted from the project directory

!!! warning
    Do not delete the `docker/mysql/` directory unless you want to lose your database. Back it up regularly.

## Common Operations

### View Logs

```bash
# All containers
docker compose logs -f

# Specific container
docker compose logs -f solder
docker compose logs -f nginx
docker compose logs -f mysql
```

### Restart the Stack

```bash
docker compose restart
```

Or to fully recreate containers (e.g. after changing `compose.yml`):

```bash
docker compose up -d
```

### Open a Shell

```bash
docker compose exec solder bash
```

### Run Artisan Commands

```bash
docker compose exec solder php artisan <command>
```

For example:

```bash
# Check migration status
docker compose exec solder php artisan migrate:status

# Clear caches
docker compose exec solder php artisan optimize:clear

# Cache configuration for better performance
docker compose exec solder php artisan optimize
```

### Stop the Stack

```bash
docker compose down
```

!!! note
    `docker compose down` stops and removes containers but does **not** delete the database files in `docker/mysql/`. Your data is preserved.

### Rebuild the Image

If the Dockerfile changes (e.g. after a Solder update), rebuild with:

```bash
docker compose build --no-cache
docker compose up -d
```

## Updating Solder

To update to the latest version:

```bash
git pull
docker compose build --no-cache
docker compose up -d
```

The setup script will automatically run any new database migrations on boot.

## Development Setup

For development, use `compose.dev.yml` which includes a named `vendor` volume (so vendor files live inside the container and don't conflict with your host) and uses PostgreSQL instead of MariaDB:

```bash
docker compose -f compose.dev.yml up -d
```

The development stack is accessible on port **8080** by default.

## HTTPS and Reverse Proxies

The default Docker setup serves Solder over HTTP on the configured `SOLDER_PORT`. For HTTPS, place a reverse proxy (such as [Caddy](https://caddyserver.com/), [Traefik](https://traefik.io/), or nginx with certbot) in front of the stack and set `APP_URL` to your `https://` address.

!!! tip
    If your reverse proxy terminates TLS, make sure it passes the `X-Forwarded-Proto` header so Solder knows the original request was HTTPS.
