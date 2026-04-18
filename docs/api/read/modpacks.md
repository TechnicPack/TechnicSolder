# Modpacks

## GET /api/modpack

List all modpacks. The modpacks returned depend on the authentication provided:

- **No auth** -- public modpacks only.
- **`cid` parameter** -- public modpacks plus any modpacks associated with the client.
- **`k` parameter** -- all modpacks, including private and hidden.
- **Bearer token** -- modpacks the token's user has access to (users with `solder_full` see all modpacks).

"Hidden" modpacks are unlisted here but remain reachable via `GET /api/modpack/{slug}`. "Private" modpacks are excluded from both endpoints and require explicit access.

### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `cid` | string | Client UUID. Grants access to client-associated modpacks. |
| `k` | string | API key. Grants full access to all modpacks. |
| `include` | string | Set to `full` to return full modpack objects instead of display names. |

### Example Request

```bash
curl https://solder.example.com/api/modpack
```

### Response (200)

```json
{
  "modpacks": {
    "hexxit": "Hexxit",
    "tekkit": "Tekkit"
  },
  "mirror_url": "https://mods.example.com/"
}
```

### Response with `include=full` (200)

```bash
curl https://solder.example.com/api/modpack?include=full
```

When `include=full` is set, each modpack value becomes a full object:

```json
{
  "modpacks": {
    "hexxit": {
      "id": 1,
      "name": "hexxit",
      "display_name": "Hexxit",
      "url": null,
      "icon": null,
      "icon_md5": null,
      "logo": null,
      "logo_md5": null,
      "background": null,
      "background_md5": null,
      "recommended": "1.0.0",
      "latest": "1.0.1",
      "builds": ["1.0.0", "1.0.1"]
    }
  },
  "mirror_url": "https://mods.example.com/"
}
```

---

## GET /api/modpack/{slug}

Show a single modpack by its slug. Hidden modpacks are omitted from `GET /api/modpack` but remain fetchable by slug without authentication; this is how the Technic Launcher accesses a pack whose slug it already knows. Private modpacks return 404 without authentication that grants access.

Authentication determines access:

- **No auth** -- public or hidden modpacks (private modpacks return 404).
- **`cid` parameter** -- also grants access to private modpacks associated with the client.
- **`k` parameter** -- any modpack.
- **Bearer token** -- modpacks the token's user has access to (users with `solder_full` see any modpack).

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug (URL-friendly name). |

### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `cid` | string | Client UUID. Grants access if the client is associated with this modpack. |
| `k` | string | API key. Grants full access regardless of modpack visibility. |

### Example Request

```bash
curl https://solder.example.com/api/modpack/hexxit
```

### Response (200)

```json
{
  "id": 1,
  "name": "hexxit",
  "display_name": "Hexxit",
  "url": null,
  "icon": null,
  "icon_md5": null,
  "logo": null,
  "logo_md5": null,
  "background": null,
  "background_md5": null,
  "recommended": "1.0.0",
  "latest": "1.0.1",
  "builds": ["1.0.0", "1.0.1"]
}
```

### Error Response (404)

```json
{
  "error": "Modpack does not exist"
}
```
