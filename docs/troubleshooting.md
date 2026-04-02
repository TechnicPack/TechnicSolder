# Troubleshooting

## Checking Logs

The first step when debugging any issue is to check the logs.

**Laravel log** -- the main application log lives at:

```
storage/logs/laravel.log
```

In Docker, you can tail it directly:

```bash
docker compose exec solder tail -f storage/logs/laravel.log
```

**Docker container logs** -- to see stdout/stderr output from the Solder container:

```bash
docker compose logs -f solder
```

**Enable debug mode** -- for more detailed error messages, set `APP_DEBUG=true` in your `.env` file:

```dotenv
APP_DEBUG=true
```

!!! danger
    Never leave `APP_DEBUG=true` in production. Debug mode exposes sensitive information including environment variables, database credentials, and full stack traces to anyone who triggers an error.

## Common Issues

### "Error in exception handler"

This almost always means Laravel cannot write to its `storage/` or `bootstrap/cache/` directories. Fix the permissions:

```bash
chgrp -R www-data storage bootstrap/cache
find storage bootstrap/cache -type d -exec chmod 775 {} \;
find storage bootstrap/cache -type f -exec chmod 664 {} \;
```

!!! note "Docker"
    If you're running Solder via Docker, file permissions are handled automatically by the setup entrypoint. You should not need to fix them manually.

### "MD5 hashing failed"

When adding or rehashing a mod version, Solder computes an MD5 checksum of the zip file. If this fails, check the following:

- **`SOLDER_REPO_LOCATION` is incorrect** -- this must point to the directory (or URL) that contains your mod files. Include a trailing slash.
- **`SOLDER_MIRROR_URL` doesn't match the actual download URL** -- the mirror URL is what the launcher uses to download files. It must be publicly accessible and point to the same repository.
- **The web server can't read the mod files** -- ensure the user running PHP (e.g., `www-data`) has read access to the repository directory.
- **The file doesn't exist at the expected path** -- Solder expects mod files at:

    ```
    [repo]/mods/[slug]/[slug]-[version].zip
    ```

    For example, if `SOLDER_REPO_LOCATION=/var/www/repo/`, a mod with slug `buildcraft` at version `7.1.23` should be at:

    ```
    /var/www/repo/mods/buildcraft/buildcraft-7.1.23.zip
    ```

### Connection Issues in Docker

If the application fails to connect to the database or cache, start by checking that all containers are running:

```bash
docker compose ps
```

All services should show a status of `Up` or `healthy`.

- **Database not ready** -- the Solder container includes a health check that waits for the database to accept connections before starting the application. If migrations fail on first boot, restart the stack with `docker compose down && docker compose up -d`.
- **Redis/Valkey connection refused** -- verify the `redis` service is running with `docker compose ps`. If using a custom Redis host, make sure `REDIS_HOST` and `REDIS_PORT` in your `.env` match the actual service.

### "Class not found" or Autoloader Errors

If you see errors about missing classes after updating or changing branches, regenerate the Composer autoloader:

```bash
composer dump-autoload
```

In Docker:

```bash
docker compose exec solder composer dump-autoload
```
