# Docker

This project ships two separate Compose stacks. They are NOT layered; each runs on its own.

- Development: `compose.dev.yml` (PostgreSQL). The standalone stack for all local work.
- Production: `compose.yml` (MariaDB). The shipped self-host stack; not for development.

## Development workflow

- Start: `docker compose -f compose.dev.yml up -d`
- Stop: `docker compose -f compose.dev.yml down`
- Run commands: `docker compose exec -T solder <cmd>` works once the stack is up (this is the
  running container the bare `docker compose exec` lines in laravel.md and testing.md target).

## Never do this

- Never run `docker compose up` with no `-f`. That starts the production MariaDB stack.
- Never merge the two files (`-f compose.yml -f compose.dev.yml`). The merge pulls MariaDB and
  the production env-injection block into dev, which: crashes the app (pgsql driver hitting
  MariaDB), runs two databases at once, and breaks test isolation (tests hit PostgreSQL instead
  of stock sqlite; CSRF and config defaults misbehave).
- The dev `solder` service injects no env vars by design; it reads the mounted `.env`, and tests
  use stock `phpunit.xml` plus `.env.testing` (sqlite). Do not add an `environment:` block to it.
