# Technic Solder

Technic Solder is an API that sits between a modpack repository and the Technic Launcher. It allows you to easily manage multiple modpacks in one single location.

Using Solder means your packs download each mod individually. The launcher checks MD5 checksums against each mod version and uses cached versions when nothing has changed — so small, incremental updates don't require redownloading the entire modpack.

Solder also links to [Technic Platform](https://www.technicpack.net/) using an API key. When linked, Technic Platform pulls modpack and mod data directly from your Solder instance — importing packs and generating detailed mod lists on your page.

## Quick Start

The fastest way to get running is with Docker:

```bash
git clone https://github.com/TechnicPack/TechnicSolder.git
cd TechnicSolder
docker compose up -d
```

The first boot automatically runs migrations, generates an app key, and creates the initial admin user. See the full [Docker setup guide](getting-started/docker.md) or the [bare-metal installation guide](getting-started/index.md).

## Initial Admin User

On first boot, an admin user is created with email `admin@admin.com` and a randomly generated password printed to the console. To set specific credentials, configure these environment variables before the first boot:

- `SOLDER_INITIAL_ADMIN_EMAIL` — admin email (default: `admin@admin.com`)
- `SOLDER_INITIAL_ADMIN_PASSWORD` — admin password (randomly generated if not set)

!!! warning
    Change these credentials immediately after your first login.

## Documentation Overview

| Section | Description |
|---------|-------------|
| [Getting Started](getting-started/index.md) | Installation, Docker setup, configuration |
| [Guides](guides/linking-technic-platform.md) | Platform linking, adding mods, updating, web servers |
| [API Reference](api/index.md) | Read and write API endpoints |
| [Troubleshooting](troubleshooting.md) | Common issues and solutions |
