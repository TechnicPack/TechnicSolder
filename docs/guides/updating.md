# Updating Solder

!!! warning "Back up before every update"
    Always back up your database and mod repository before updating. If something goes wrong, you can restore from backup and try again.

## Standard Update (Bare-Metal)

Put Solder into maintenance mode, pull the latest code, update dependencies, run migrations, and bring it back up:

```bash
php artisan down
git pull
composer install --no-dev --no-interaction
npm ci
npm run build
php artisan migrate
php artisan optimize
php artisan up
```

!!! note
    If `git pull` fails because untracked files would be overwritten, you may need to reset before pulling:

    ```bash
    git reset --hard <version-tag>
    git pull
    ```

    Replace `<version-tag>` with the tag of the version you were on before the update.

## Docker Update

For Docker installations, pull the latest code and rebuild:

```bash
git pull
docker compose up -d --build
```

The container entrypoint handles migrations and optimization automatically on startup.

## Branch Rename

The `dev` branch has been renamed to `main`, and the `master` branch has been removed. If your local clone still tracks the old branch, update it with:

```bash
git branch -m dev main
git fetch origin
git branch -u origin/main main
git remote set-head origin -a
```

## Version-Specific Notes

### v1.0.0

**Requires PHP 8.4+, Node.js 20+, and npm.**

- Laravel upgraded from 12 to **13**
- Frontend rebuilt with **Vite 8**, Tailwind CSS v4, and Alpine.js — `npm ci && npm run build` is now a required deployment step
- Authentication replaced with **Laravel Fortify** (2FA support) and **Laravel Sanctum** (API tokens)
- New environment variables: `MAIL_ENABLED`, `SOLDER_CORS_ORIGINS`, `SOLDER_INITIAL_ADMIN_EMAIL`, `SOLDER_INITIAL_ADMIN_PASSWORD`
- Password policy now requires minimum 8 characters and checks against breached databases
- Docker: Redis image replaced by **Valkey**

Run `php artisan migrate` to create the new `personal_access_tokens`, `password_reset_tokens` tables and add 2FA columns to `users`.

### v0.8.0

**Requires PHP 8.2+.** Install PHP 8.2 or later and configure your web server to use it.

You must also update your `.env` file:

1. Rename `BROADCAST_DRIVER` to `BROADCAST_CONNECTION`:

    ```ini
    # Before
    BROADCAST_DRIVER=log
    # After
    BROADCAST_CONNECTION=log
    ```

2. Rename `CACHE_DRIVER` to `CACHE_STORE`:

    ```ini
    # Before
    CACHE_DRIVER=file
    # After
    CACHE_STORE=file
    ```

3. Add the following line (see `.env.example` for details):

    ```ini
    HASH_VERIFY=false
    ```

### v0.7.15

**Requires PHP 8.1+.** Install PHP 8.1 or later and configure your web server to use it.

### v0.7.8

**Requires PHP 8.0+.** Install PHP 8.0 or later and configure your web server to use it.

### Pre-v0.7

!!! danger "Full reinstall required"
    Updating from versions before v0.7 requires a complete reinstall. Back up your database and modpack resources (images), delete the Solder installation, and follow the [Getting Started](../getting-started/index.md) guide to install fresh.

After reinstalling, you will need to fix the Laravel migration table. The old table was named `laravel_migrations` with three columns (`bundle`, `name`, `batch`). The new table is named `migrations` with two columns (`migration`, `batch`).

To migrate the old table:

1. Delete the `bundle` column.
2. Rename the `name` column to `migration`.
3. Rename the table:

    ```sql
    ALTER TABLE laravel_migrations DROP COLUMN bundle;
    ALTER TABLE laravel_migrations CHANGE name migration VARCHAR(255);
    RENAME TABLE laravel_migrations TO migrations;
    ```

4. Run `php artisan migrate` to apply any remaining migrations.
