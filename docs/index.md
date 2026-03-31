# Technic Solder

Technic Solder is an API that sits between a modpack repository and the Technic Launcher. It allows you to easily manage multiple modpacks in one single location.

Using Solder means your packs download each mod individually. The launcher checks MD5 checksums against each mod version and uses cached versions when nothing has changed — so small, incremental updates don't require redownloading the entire modpack.

Solder also interfaces with the [Technic Platform](https://www.technicpack.net/) using an API key. When linked, Solder can directly interact with your Platform account — importing packs and generating detailed mod lists on your Platform page.

## Quick Start

The fastest way to get running is with Docker:

```bash
git clone https://github.com/TechnicPack/TechnicSolder.git
cd TechnicSolder
docker compose up -d
```

The first boot automatically runs migrations, generates an app key, and creates the default admin user. See the full [Docker setup guide](getting-started/docker.md) or the [bare-metal installation guide](getting-started/index.md).

## Default Credentials

After setup, log in at your Solder URL with:

- **Email:** `admin@admin.com`
- **Password:** `admin`

!!! warning
    Change these credentials immediately after your first login.

## Documentation Overview

| Section | Description |
|---------|-------------|
| [Getting Started](getting-started/index.md) | Installation, Docker setup, configuration |
| [Guides](guides/linking-technic-platform.md) | Platform linking, adding mods, updating, web servers |
| [API Reference](api/index.md) | Read and write API endpoints |
| [Troubleshooting](troubleshooting.md) | Common issues and solutions |
