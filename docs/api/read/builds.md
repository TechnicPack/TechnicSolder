# Builds

## GET /api/modpack/{slug}/{version}

Show a specific build of a modpack. The same authentication rules as the modpack endpoints apply -- without auth only public modpack builds are accessible. Bearer tokens can also access private builds when the token's user has access to the modpack.

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug. |
| `version` | string | The build version string. |

### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `cid` | string | Client UUID. Grants access if the client is associated with this modpack. |
| `k` | string | API key. Grants full access regardless of modpack visibility. |
| `include` | string | Set to `mods` to include additional mod metadata in the response. |

### Example Request

```bash
curl https://solder.yourdomain.com/api/modpack/hexxit/1.0.0
```

### Response (200)

```json
{
  "id": 1,
  "minecraft": "1.12.2",
  "java": "1.8",
  "memory": 2048,
  "forge": "14.23.5.2847",
  "mods": [
    {
      "id": 1,
      "name": "rei-minimap",
      "version": "1.0.0",
      "md5": "abc123def456",
      "filesize": 5242880,
      "url": "https://mods.example.com/mods/rei-minimap/rei-minimap-1.0.0.zip"
    }
  ]
}
```

### Response with `include=mods` (200)

```bash
curl https://solder.yourdomain.com/api/modpack/hexxit/1.0.0?include=mods
```

When `include=mods` is set, each mod object includes additional metadata fields:

```json
{
  "id": 1,
  "minecraft": "1.12.2",
  "java": "1.8",
  "memory": 2048,
  "forge": "14.23.5.2847",
  "mods": [
    {
      "id": 1,
      "name": "rei-minimap",
      "version": "1.0.0",
      "md5": "abc123def456",
      "filesize": 5242880,
      "url": "https://mods.example.com/mods/rei-minimap/rei-minimap-1.0.0.zip",
      "pretty_name": "Rei's Minimap",
      "author": "ReiFNSK",
      "description": "A minimap mod",
      "link": "https://example.com"
    }
  ]
}
```

### Error Responses

**Modpack not found (404):**

```json
{
  "error": "Modpack does not exist"
}
```

**Build not found (404):**

```json
{
  "error": "Build does not exist"
}
```
