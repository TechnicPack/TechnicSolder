# Contributing

## Development Environment

The dev environment uses Docker with PostgreSQL and Redis. It runs on port 8080 to avoid conflicting with a production instance.

### Prerequisites

- Docker and Docker Compose v2
- Git

### Setup

```bash
git clone https://github.com/TechnicPack/TechnicSolder.git
cd TechnicSolder
docker compose -f compose.dev.yml up -d
```

First boot runs `docker/dev-setup.sh` which:

1. Installs all Composer dependencies (including dev dependencies)
2. Creates a `.env` from `.env.example` with dev-appropriate defaults (debug on, PostgreSQL, Redis)
3. Generates an `APP_KEY`
4. Runs database migrations
5. Builds frontend assets
6. Sets file permissions

Solder is then available at [http://localhost:8080](http://localhost:8080).

### Default Credentials

- **Email:** `admin@admin.com`
- **Password:** `admin`

!!! note
    Run `docker compose -f compose.dev.yml exec solder php artisan solder:setup` to create the default admin user if it doesn't exist.

### How It Differs from Production

| | Production (`compose.yml`) | Development (`compose.dev.yml`) |
|---|---|---|
| Port | 80 | 8080 |
| Database | MariaDB | PostgreSQL |
| Dev dependencies | Excluded (`--no-dev`) | Included |
| `APP_DEBUG` | `false` | `true` |
| `APP_ENV` | `production` | `local` |
| Vendor directory | Inside container | Named volume (persists across rebuilds) |
| Cache/session | Valkey | Redis |

The named `vendor` volume means `vendor/` lives inside Docker, not on your host filesystem. This avoids cross-platform filesystem issues and speeds up autoloading.

## Common Commands

All commands run inside the container via `docker compose -f compose.dev.yml exec solder`:

```bash
# Run the test suite
docker compose -f compose.dev.yml exec solder php artisan test

# Run a specific test class
docker compose -f compose.dev.yml exec solder php artisan test --filter=ApiTest

# Code style fixing (Laravel Pint)
docker compose -f compose.dev.yml exec solder ./vendor/bin/pint

# Static analysis (Larastan/PHPStan)
docker compose -f compose.dev.yml exec solder ./vendor/bin/phpstan

# Install/update Composer dependencies
docker compose -f compose.dev.yml exec solder composer install

# Run database migrations
docker compose -f compose.dev.yml exec solder php artisan migrate

# Open a shell inside the container
docker compose -f compose.dev.yml exec solder bash
```

!!! tip
    You can alias `dc` to `docker compose -f compose.dev.yml` in your shell to save typing.

## Testing

Tests use an in-memory SQLite database configured in `.env.testing` — they don't touch your development database.

```bash
docker compose -f compose.dev.yml exec solder php artisan test
```

## Code Style

This project uses [Laravel Pint](https://laravel.com/docs/pint) for code formatting. Run it before submitting a pull request:

```bash
docker compose -f compose.dev.yml exec solder ./vendor/bin/pint
```

## Static Analysis

[Larastan](https://github.com/larastan/larastan) (PHPStan for Laravel) is used for static analysis:

```bash
docker compose -f compose.dev.yml exec solder ./vendor/bin/phpstan
```

## Pull Requests

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Run tests, Pint, and PHPStan
5. Submit a pull request
