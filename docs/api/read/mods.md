# Mods

!!! note
    The mod API can be disabled by setting `SOLDER_DISABLE_MOD_API=true` in your `.env` file. When disabled, all endpoints on this page return a 404 response.

## GET /api/mod

List all mods.

### Example Request

```bash
curl https://solder.yourdomain.com/api/mod
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
curl https://solder.yourdomain.com/api/mod/rei-minimap
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

Show a specific version of a mod. Returns the MD5 checksum, file size, and download URL for the mod archive.

The download URL follows the format: `{mirror_url}/mods/{modname}/{modname}-{version}.zip`

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The mod slug. |
| `version` | string | The mod version string. |

### Example Request

```bash
curl https://solder.yourdomain.com/api/mod/rei-minimap/1.0.0
```

### Response (200)

```json
{
  "id": 1,
  "md5": "abc123def456",
  "filesize": 5242880,
  "url": "https://mods.example.com/mods/rei-minimap/rei-minimap-1.0.0.zip"
}
```

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
