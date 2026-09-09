# Changelog

All notable changes to Technic Solder will be documented in this file.

## [Unreleased]

## [1.3.1] - 2026-09-09

### Changed

- Inter fonts are now bundled and self-hosted through Vite instead of loaded from Bunny by visitors' browsers, with Fontaine-adjusted fallback metrics to reduce layout shifts. Font updates require rebuilding and deploying the frontend assets.

## [1.3.0] - 2026-09-09

### Changed

- Updated PHP and frontend dependencies, including Laravel 13.30.1, Debugbar 4.4.3, Larastan 3.11, Alpine.js 3.17.1, and the Node.js 24.20 build image; Guzzle remains on 7.15 pending the production PHP 8.5 upgrade.
- Synchronized the application scaffold with `laravel/laravel` v13.10.1, including Composer lifecycle hooks, PHPUnit configuration, entrypoints, Apache XSRF header forwarding, and Vite/Tailwind defaults while retaining Solder's Docker workflow and cache/session formats.
- Mail examples now document the optional `MAIL_SCHEME` override while preserving automatic SMTP scheme selection, including in Docker. Existing working mail configurations require no `.env` migration; the already-unused `MAIL_ENCRYPTION` variable may be removed.
- Development PostgreSQL and Redis ports are exposed on loopback for host-run SolderJS.

### Fixed

- API modpack, mod, build-version, mod-version, and token listings now use ascending record IDs for deterministic ordering. Build mods retain natural, case-insensitive name ordering with mod-version IDs breaking ties.
- API routing errors now return JSON even when a client requests HTML; web routing errors retain HTML responses.
- The documented `APP_TIMEZONE` setting now takes effect instead of always using UTC. UTC remains the default; existing non-UTC values now affect application date handling.

## [1.2.0] - 2026-08-27

### Added

- Bearer-token users with `mods_manage` or `solder_full` permission can read `/api/mod` endpoints when public mod API access is disabled with `SOLDER_DISABLE_MOD_API=true`
- `GET /api/mod/{slug}/{version}` responses now include accessible build memberships and their owning modpack metadata
- Build mod list filtering by mod name, slug, and selected version

### Changed

- Development Compose stack now uses the isolated `technicsolder-dev` project instead of sharing production containers and volumes
- Password fields now identify current and new credentials correctly to password managers
- Distributed environment example now enables persistent Redis connections to prevent ephemeral-port exhaustion under sustained high concurrency
- npm dependency lifecycle scripts are disabled and dependency updates are delayed for seven days to reduce supply-chain exposure; Renovate security updates remain exempt from the delay
- Updated PHP, frontend, build, and CI dependencies, including Laravel 13.26, Guzzle 7.15, Alpine.js 3.16, Tailwind CSS 4.3, Vite 8.2, Node.js 24.18, `actions/checkout` 7, and `actions/setup-python` 7

### Fixed

- PostCSS and nanoid updated to fix source map path traversal and unbounded generator loop vulnerabilities
- Delegated user managers can no longer grant permissions they do not hold or manage users with greater permissions or broader modpack access
- The final `solder_full` user can no longer demote themselves, regardless of their database ID
- Profile-only and password-only user updates no longer revoke permissions omitted from the request
- Password changes now require the acting user's current password, including when a manager resets another user's password; malformed password values return a validation error
- Login throttling now canonicalizes email casing and Unicode variants, preventing equivalent addresses from receiving separate rate-limit buckets
- Login details, update checks, and password rehashing now occur only after authentication and two-factor challenges complete successfully
- Users can no longer delete their own accounts

## [1.1.3] - 2026-04-18

### Fixed

- Hidden modpacks (not marked private) were returning 404 on direct API access, causing the Technic Launcher to show them as "Offline". Restored the documented behavior: hidden packs are omitted from `/api/modpack` listing but remain fetchable by slug via `/api/modpack/{slug}` and `/api/modpack/{slug}/{version}` without a `cid`. Regression introduced in the API refactor that consolidated access checks behind `Modpack::isAccessibleBy()`.

## [1.1.2] - 2026-04-10

### Fixed

- Pagination buttons corrupted on middle pages due to duplicate Alpine.js `x-for` key when two ellipsis entries existed

## [1.1.1] - 2026-04-09

### Added

- Notes exposed in read API for authenticated users with `mods_manage` or `solder_full` permission
- `PUT /api/mod/{slug}/{version}` endpoint for updating mod version fields
- Notes editing UI in expanded version rows on the mod view page

### Fixed

- Sort persistence: `storageKey` computed as property instead of getter (getter wasn't available during Alpine `init()`)
- Version notes save using correct JSON content type instead of form-encoded data

## [1.1.0] - 2026-04-09

### Added

- Clone entire modpack with all builds and mod assignments (web UI + API)
- Clone builds across modpacks (grouped dropdown showing all accessible modpacks)
- Save All button for batch mod version updates in builds
- Private notes field on mods and mod versions (excluded from read API)
- Rehash All button for mod versions (sequential with progress and cancel)
- Unused mod versions section on dashboard
- CSV export for build mod lists
- Data table sort direction persisted in localStorage
- `grantModpackAccess()` method on `UserPermission` model
- Database indexes on `modversions.mod_id`, `builds.modpack_id`, `user_permissions.user_id`, `client_modpack.client_id`, `client_modpack.modpack_id`, `clients.uuid`, `keys.api_key`

### Fixed

- MD5 file download skipped when user provides hash manually (no more timeouts on large files)
- MD5 hash format validated (must be 32-character hex string)
- Remote MD5 error messages now show actionable hints instead of raw cURL errors
- Cross-modpack clone authorization check (was missing, allowed unauthorized cloning)
- Clone validation runs before build creation in API (no orphaned builds on failure)
- Slug uniqueness validated after `Str::slug()` normalization (prevents bypass)
- `findOrFail` used consistently in `anyModify` batch-version case
- PHP 8.4 deprecation warning in `getModpacksAttribute()` null handling
- Clone build dropdown sorted by modpack name (current modpack first) with newest builds first

### Changed

- Modpack permission assignment extracted from duplicated controller blocks into `UserPermission::grantModpackAccess()`
- `clone_from_modpack` API parameter added for cross-pack build cloning (backward compatible)

## [1.0.3] - 2026-04-08

### Fixed

- Non-standard placeholder domains replaced with `example.com`
- Guest redirect broken by Laravel framework v13.4.0 middleware bug ([laravel/framework#59600](https://github.com/laravel/framework/issues/59600))

## [1.0.2] - 2026-04-06

### Fixed

- Build clone allowing cross-modpack data leak (IDOR)
- Null dereference in modpack modify handler on invalid IDs
- CORS middleware now validates request origin against allowed origins list
- Redundant prefix in release title

### Changed

- Replaced Dependabot with Renovate for all dependencies
- Added PHP 8.5 to CI test matrix
- Bumped nick-fields/retry to v4 for Node.js 24 compatibility

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

[1.3.1]: https://github.com/TechnicPack/TechnicSolder/compare/v1.3.0...v1.3.1
[1.3.0]: https://github.com/TechnicPack/TechnicSolder/compare/v1.2.0...v1.3.0
[1.2.0]: https://github.com/TechnicPack/TechnicSolder/compare/v1.1.3...v1.2.0
[1.1.3]: https://github.com/TechnicPack/TechnicSolder/compare/v1.1.2...v1.1.3
[1.1.2]: https://github.com/TechnicPack/TechnicSolder/compare/v1.1.1...v1.1.2
[1.1.1]: https://github.com/TechnicPack/TechnicSolder/compare/v1.1.0...v1.1.1
[1.1.0]: https://github.com/TechnicPack/TechnicSolder/compare/v1.0.3...v1.1.0
[1.0.3]: https://github.com/TechnicPack/TechnicSolder/compare/v1.0.2...v1.0.3
[1.0.2]: https://github.com/TechnicPack/TechnicSolder/compare/v1.0.1...v1.0.2
[1.0.1]: https://github.com/TechnicPack/TechnicSolder/compare/v1.0.0...v1.0.1
[1.0.0]: https://github.com/TechnicPack/TechnicSolder/compare/v0.12.9...v1.0.0
