# Mods

!!! note
    The mod API can be disabled by setting `SOLDER_DISABLE_MOD_API=true` in your `.env` file. When disabled, anonymous requests and `cid` or `k` query credentials receive a 404 response. Sanctum bearer-token users with `mods_manage` or `solder_full` permission can still read these endpoints.

## GET /api/mod

List all mods.

### Example Request

```bash
curl https://solder.example.com/api/mod
```

### Response (200)

```json
{
  "mods": {
    "rei-minimap": "Rei's Minimap",
    "buildcraft": "BuildCraft"
  }
}
```

---

## GET /api/mod/{slug}

Show a single mod by its slug.

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The mod slug (URL-friendly name). |

### Example Request

```bash
curl https://solder.example.com/api/mod/rei-minimap
```

### Response (200)

```json
{
  "id": 1,
  "name": "rei-minimap",
  "pretty_name": "Rei's Minimap",
  "author": "ReiFNSK",
  "description": "A minimap mod",
  "link": "https://example.com",
  "versions": ["1.0.0", "1.0.1"]
}
```

### Error Response (404)

```json
{
  "error": "Mod does not exist"
}
```

---

## GET /api/mod/{slug}/{version}

Show a specific version of a mod. Returns the MD5 checksum, file size, download URL, and accessible build memberships for the mod archive.

The download URL follows the format: `{mirror_url}/mods/{modname}/{modname}-{version}.zip`

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The mod slug. |
| `version` | string | The mod version string. |

### Example Request

```bash
curl https://solder.example.com/api/mod/rei-minimap/1.0.0
```

### Response (200)

```json
{
  "id": 1,
  "md5": "abc123def456",
  "filesize": 5242880,
  "url": "https://mods.example.com/mods/rei-minimap/rei-minimap-1.0.0.zip",
  "builds": [
    {
      "id": 10,
      "version": "1.2.0",
      "modpack": {
        "id": 3,
        "name": "example-pack",
        "display_name": "Example Pack"
      }
    }
  ]
}
```

`builds` contains only published builds visible to the current request. Public and hidden modpacks are included without authentication. Private modpacks and private builds require access through a client UUID, API key, or bearer-token user; unpublished builds are always omitted.

### Error Responses

**Mod not found (404):**

```json
{
  "error": "Mod does not exist"
}
```

**Version not found (404):**

```json
{
  "error": "Mod version does not exist"
}
```
