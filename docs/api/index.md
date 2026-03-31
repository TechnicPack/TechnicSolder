# API Overview

Technic Solder exposes a JSON API used by the Technic Launcher to download modpacks, and by administrators to manage content programmatically.

## Base URL

All endpoints are relative to your Solder installation URL:

```
https://solder.yourdomain.com/api/
```

## Authentication

The API supports three authentication methods depending on the endpoint:

### Read API — Query Parameters

Public read endpoints accept optional query parameters for access control:

| Parameter | Description |
|-----------|-------------|
| `cid` | Client UUID. Grants access to modpacks associated with this client. |
| `k` | API Key. Grants full access to all modpacks, including private and hidden ones. |

Without either parameter, only public modpacks are returned.

### Write API — Bearer Token

All write endpoints require a Sanctum bearer token in the `Authorization` header:

```
Authorization: Bearer 1|abc123def456...
```

Tokens are created via the [Token API](write/tokens.md) or from the Solder web UI under your user profile. See [Authentication](write/authentication.md) for details.

## Response Format

All responses are JSON. Successful responses return the resource data directly. Error responses follow this format:

```json
{"error": "Description of what went wrong."}
```

Validation errors (422) return field-level details:

```json
{"error": {"field_name": ["Error message."]}}
```

## Caching

Read API responses are cached for up to 5 minutes. Write operations automatically invalidate related caches.

## Rate Limiting

The `GET /api/verify/{key}` endpoint is rate-limited. All other endpoints use default Laravel rate limiting.

## Disabling the Mod API

Set `SOLDER_DISABLE_MOD_API=true` in your `.env` to disable the `/api/mod` endpoints. This returns a 404 for all mod-related read requests. Write endpoints are unaffected.
