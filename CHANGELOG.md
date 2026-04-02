# Changelog

All notable changes to Technic Solder will be documented in this file.

## [1.0.1] - 2026-04-03

### Fixed

- Sidebar modpack icons using filesystem paths instead of `icon_url`, causing broken images for modpacks with custom art
- Tests requiring Vite manifest to pass — added `withoutVite()` to base TestCase

### Changed

- Release workflow now extracts notes from CHANGELOG.md instead of auto-generating

## [1.0.0] - 2026-04-02

### Added

- **Two-factor authentication** (TOTP with QR codes and recovery codes) via Laravel Fortify
- **API token authentication** for write API endpoints via Laravel Sanctum (`sldr_`-prefixed tokens)
- **Write API** with full CRUD for modpacks, builds, build mods, mods, mod versions, and clients
- **Token management** endpoints and web UI for creating/revoking personal API tokens
- **Password reset** via email (requires `MAIL_ENABLED=true`)
- **Policy-based authorization** replacing custom middleware, with a global gate for `solder_full` users
- **Security headers** middleware (X-Content-Type-Options, X-Frame-Options, Referrer-Policy, Permissions-Policy)
- **Rate limiting** on login (5/min), 2FA (5/min), and API key verification (10/min)
- **`solder:setup` command** for initial admin user creation with interactive prompts or `SOLDER_INITIAL_ADMIN_*` env vars
- **Docker development environment** (`compose.dev.yml`) with PostgreSQL and named vendor volume
- **MkDocs documentation** site with installation, API reference, and configuration guides
- Modpack search filter in sidebar
- Mod list filtering by user permissions on build view
- Dashboard filtering of builds by user modpack permissions
- Slug auto-highlight on mod view page
- Client-side searchable dropdowns with duplicate prevention on build mod management
- Java 21 and 25 to supported versions enum
- `SOLDER_CORS_ORIGINS` environment variable for configurable CORS
- `MAIL_ENABLED` environment variable to gate email functionality

### Changed

- **PHP 8.4 required** (was 8.2)
- **Laravel 13** (was 12)
- **PHPUnit 12** (was 11)
- **Vite 8** with laravel-vite-plugin 3 (was Vite 6 / plugin 1)
- **Tailwind CSS v4** with Alpine.js v3 frontend (fully modernized UI)
- **Redis replaced by Valkey** in Docker compose
- **Debugbar** changed from `barryvdh/laravel-debugbar` to `fruitcake/laravel-debugbar` v4
- **Session encryption** enabled by default
- **Password policy** now enforces minimum 8 characters and checks against breached databases (was min 3)
- Authentication system replaced from custom AuthController to Laravel Fortify
- All models now use explicit `$fillable` arrays instead of `$guarded = []`
- API key and client UUID verification uses timing-safe `hash_equals()`
- Privilege escalation prevention: only `solder_full` users can grant `solder_full`
- Apache documentation updated to use `mod_proxy_fcgi` with PHP-FPM
- Test fixtures moved from `public/` to `tests/fixtures/`
- Docker entrypoint warns instead of silently failing when frontend assets are missing

### Removed

- Custom authentication middleware (`SolderMods`, `SolderModpacks`, `SolderUsers`, `SolderKeys`, `SolderClients`, `Modpack`, `Build`)
- Custom `AuthController` (replaced by Fortify-managed routes)
- Insecure default admin password in non-interactive setup (now generates random password)
- Inline admin user creation in database migrations (moved to `solder:setup` command)

### Fixed

- 2FA bypass in Fortify login flow
- Deletion of last modpack build without crashing
- Undefined `$modpack` in build authorization check
- Double password hashing
- Silently discarded mass-assignment attributes now throw
- Error message display for invalid 2FA recovery codes
- Sidebar overflow when modpack list is long

[1.0.1]: https://github.com/TechnicPack/TechnicSolder/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/TechnicPack/TechnicSolder/compare/v0.12.9...v1.0.0
