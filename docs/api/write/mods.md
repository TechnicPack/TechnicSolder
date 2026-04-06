# Mods

## POST /api/mod

Create a new mod.

**Permission required:** `mods_create`

### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | Slug-style unique identifier (e.g. `buildcraft`). Must be unique. |
| `pretty_name` | string | Yes | Human-readable display name (e.g. `BuildCraft`). |
| `author` | string | No | Mod author name. |
| `description` | string | No | Short description of the mod. |
| `link` | string | No | URL to the mod's homepage. Must be a valid URL or null. |

### Example Request

```bash
curl -X POST https://solder.example.com/api/mod \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "buildcraft",
    "pretty_name": "BuildCraft",
    "author": "SpaceToad",
    "description": "Extending Minecraft with pipes, auto-crafting, and more.",
    "link": "https://www.mod-buildcraft.com"
  }'
```

### Response (201)

```json
{
  "id": 1,
  "name": "buildcraft",
  "pretty_name": "BuildCraft",
  "author": "SpaceToad",
  "description": "Extending Minecraft with pipes, auto-crafting, and more.",
  "link": "https://www.mod-buildcraft.com",
  "created_at": "2026-03-31T12:00:00.000000Z",
  "updated_at": "2026-03-31T12:00:00.000000Z"
}
```

### Error Response (422)

```json
{
  "error": {
    "name": ["The name has already been taken."]
  }
}
```

---

## PUT /api/mod/{slug}

Update an existing mod.

**Permission required:** `mods_manage`

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The mod slug (name). |

### Request Body

All fields are optional. Only included fields are updated.

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Slug-style identifier. Must be unique. |
| `pretty_name` | string | Display name. |
| `author` | string | Mod author name. |
| `description` | string | Short description. |
| `link` | string | Homepage URL. Must be a valid URL or null. |

### Example Request

```bash
curl -X PUT https://solder.example.com/api/mod/buildcraft \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "pretty_name": "BuildCraft 2",
    "author": "SpaceToad & Developers"
  }'
```

### Response (200)

```json
{
  "id": 1,
  "name": "buildcraft",
  "pretty_name": "BuildCraft 2",
  "author": "SpaceToad & Developers",
  "description": "Extending Minecraft with pipes, auto-crafting, and more.",
  "link": "https://www.mod-buildcraft.com",
  "created_at": "2026-03-31T12:00:00.000000Z",
  "updated_at": "2026-03-31T12:05:00.000000Z"
}
```

### Error Responses

**Mod not found (404):**

```json
{
  "error": "Mod not found."
}
```

**Validation error (422):**

```json
{
  "error": {
    "name": ["The name has already been taken."]
  }
}
```

---

## DELETE /api/mod/{slug}

Delete a mod and all of its versions.

**Permission required:** `mods_delete`

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The mod slug (name). |

### Example Request

```bash
curl -X DELETE https://solder.example.com/api/mod/buildcraft \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "success": "Mod deleted."
}
```

### Error Response (404)

```json
{
  "error": "Mod not found."
}
```

!!! warning
    Deleting a mod also deletes **all of its versions** and detaches them from all builds. This action cannot be undone.

---

## POST /api/mod/{slug}/version

Create a new version for a mod.

**Permission required:** `mods_manage`

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The mod slug (name). |

### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `version` | string | Yes | Version string (e.g. `7.1.0`). Must be unique within the mod. |
| `md5` | string | Yes | MD5 hash of the mod archive file. |
| `filesize` | integer | No | File size in bytes. |

### Example Request

```bash
curl -X POST https://solder.example.com/api/mod/buildcraft/version \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "version": "7.1.0",
    "md5": "d41d8cd98f00b204e9800998ecf8427e",
    "filesize": 5242880
  }'
```

### Response (201)

```json
{
  "id": 1,
  "mod_id": 1,
  "version": "7.1.0",
  "md5": "d41d8cd98f00b204e9800998ecf8427e",
  "filesize": 5242880,
  "created_at": "2026-03-31T12:00:00.000000Z",
  "updated_at": "2026-03-31T12:00:00.000000Z"
}
```

### Error Responses

**Mod not found (404):**

```json
{
  "error": "Mod not found."
}
```

**Duplicate version (422):**

```json
{
  "error": "Version already exists for this mod."
}
```

**Validation error (422):**

```json
{
  "error": {
    "version": ["The version field is required."],
    "md5": ["The md5 field is required."]
  }
}
```

---

## DELETE /api/mod/{slug}/{version}

Delete a specific version of a mod.

**Permission required:** `mods_manage`

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The mod slug (name). |
| `version` | string | The version string to delete. |

### Example Request

```bash
curl -X DELETE https://solder.example.com/api/mod/buildcraft/7.1.0 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "success": "Mod version deleted."
}
```

### Error Responses

**Mod not found (404):**

```json
{
  "error": "Mod not found."
}
```

**Version not found (404):**

```json
{
  "error": "Mod version not found."
}
```

**Version in use (409):**

```json
{
  "error": "Mod version is in use by 3 build(s) and cannot be deleted."
}
```

!!! note
    A mod version cannot be deleted while it is attached to any builds. Remove it from all builds first, then delete it.
