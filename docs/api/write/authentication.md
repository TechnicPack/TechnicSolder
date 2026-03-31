# Authentication

All write API endpoints require a Sanctum bearer token in the `Authorization` header.

## Creating a Token

### Via the API

If you already have a token, you can create additional ones:

```bash
curl -X POST https://solder.yourdomain.com/api/token \
  -H "Authorization: Bearer YOUR_EXISTING_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"name": "CI Deploy Token"}'
```

#### Response (201)

```json
{
  "token": {
    "id": 1,
    "name": "CI Deploy Token",
    "plaintext": "1|abc123def456...",
    "created_at": "2026-03-31T12:00:00.000000Z"
  }
}
```

!!! warning
    The `plaintext` value is only returned once, at creation time. Store it securely -- you will not be able to retrieve it again.

### Via the Web UI

1. Log in to the Solder admin dashboard.
2. Navigate to your user profile.
3. Create a new API token by entering a name and clicking **Create**.
4. Copy the token immediately -- it will not be shown again.

## Using a Token

Include the token in the `Authorization` header of every request:

```
Authorization: Bearer 1|abc123def456...
```

### Example Request

```bash
curl -X POST https://solder.yourdomain.com/api/mod \
  -H "Authorization: Bearer 1|abc123def456..." \
  -H "Content-Type: application/json" \
  -d '{"name": "buildcraft", "pretty_name": "BuildCraft"}'
```

## Permissions

Tokens inherit the permissions of the user who created them. The following permissions control access to write endpoints:

| Permission | Grants |
|------------|--------|
| `modpacks_create` | Create new modpacks |
| `modpacks_manage` | Update modpacks, manage builds and build mods |
| `modpacks_delete` | Delete modpacks |
| `mods_create` | Create new mods |
| `mods_manage` | Update mods, create/delete mod versions |
| `mods_delete` | Delete mods |
| `solder_clients` | Manage clients (create, update, delete) |

!!! note
    Modpack write operations also check that the user has access to the specific modpack. Modpack access is configured per-user in the admin dashboard.

Users with the `solder_full` permission bypass all permission checks.

## Managing Tokens

See the [Tokens API](tokens.md) for listing and revoking tokens programmatically.
