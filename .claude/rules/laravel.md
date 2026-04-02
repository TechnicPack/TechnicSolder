# Laravel

- Use `docker compose exec -T solder php artisan make:` to create new files (migrations, controllers, models, etc.). Pass `--no-interaction` and the correct `--options`.
- List available commands: `docker compose exec -T solder php artisan list`. Check parameters: `docker compose exec -T solder php artisan [command] --help`.
- For generic PHP classes: `docker compose exec -T solder php artisan make:class`.
- When creating models, also create factories and seeders. Ask the user about additional options via `docker compose exec -T solder php artisan make:model --help`.
- For APIs, default to Eloquent API Resources and API versioning unless existing routes don't — follow application convention.
- Prefer named routes and the `route()` function for links.

## Routes & Config

- Inspect routes: `docker compose exec -T solder php artisan route:list` (filter: `--method=GET`, `--name=users`, `--path=api`, `--except-vendor`, `--only-vendor`).
- Read config: `docker compose exec -T solder php artisan config:show app.name`, or read config files directly from `config/`.
- Check environment variables by reading `.env` directly.

## Tinker

- Use for debugging/testing in app context. Don't create models without approval — prefer tests with factories. Prefer existing Artisan commands over custom tinker code.
- Always single quotes to prevent shell expansion: `docker compose exec -T solder php artisan tinker --execute 'Your::code();'`
  - Double quotes for PHP strings inside: `'User::where("active", true)->count();'`

## Vite Error

- `ViteException: Unable to locate file in Vite manifest` — run `npm run build` or ask user to run `npm run dev` or `composer run dev`.
