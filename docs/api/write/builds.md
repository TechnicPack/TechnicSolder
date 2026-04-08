# Builds

## POST /api/modpack/{slug}/build

Create a new build for a modpack.

**Permission required:** `modpacks_manage` + access to the modpack

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug. |

### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `version` | string | Yes | Build version string (e.g. `1.0.0`). Must be unique within the modpack. |
| `minecraft` | string | Yes | Minecraft version (e.g. `1.20.1`). |
| `forge` | string | No | Forge version string. |
| `is_published` | boolean | No | Whether the build is visible in the API. Defaults to `false`. |
| `private` | boolean | No | Restrict to authorized clients. Defaults to `false`. |
| `min_java` | string | No | Minimum Java version (e.g. `17`). |
| `min_memory` | integer | No | Minimum memory in MB (e.g. `2048`). |
| `clone_from` | string | No | Version string of an existing build. All mod assignments from the source build will be copied to the new build. Defaults to searching the current modpack unless `clone_from_modpack` is specified. |
| `clone_from_modpack` | string | No | Slug of the modpack containing the source build. Use with `clone_from` to clone from a different modpack. |

### Example Request

```bash
curl -X POST https://solder.example.com/api/modpack/hexxit/build \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "version": "1.0.0",
    "minecraft": "1.20.1",
    "forge": "47.2.0",
    "is_published": true,
    "min_java": "17",
    "min_memory": 2048
  }'
```

### Response (201)

```json
{
  "id": 1,
  "modpack_id": 1,
  "version": "1.0.0",
  "minecraft": "1.20.1",
  "forge": "47.2.0",
  "is_published": true,
  "private": false,
  "min_java": "17",
  "min_memory": 2048,
  "created_at": "2026-03-31T12:00:00.000000Z",
  "updated_at": "2026-03-31T12:00:00.000000Z"
}
```

### Cloning Mods from Another Build

Use the `clone_from` parameter to copy all mod assignments from an existing build. This is useful when creating a new version that starts with the same mod list.

```bash
curl -X POST https://solder.example.com/api/modpack/hexxit/build \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "version": "1.0.1",
    "minecraft": "1.20.1",
    "clone_from": "1.0.0"
  }'
```

To clone from a build in a different modpack, include `clone_from_modpack`:

```bash
curl -X POST https://solder.yourdomain.com/api/modpack/tekkit/build \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "version": "1.0.0",
    "minecraft": "1.20.1",
    "clone_from": "1.0.0",
    "clone_from_modpack": "hexxit"
  }'
```

If the clone source build or modpack is not found, the request returns a `404` error. If the user does not have permission to access the source modpack, it returns `403`. The build is not created on failure.

### Error Responses

**Modpack not found (404):**

```json
{
  "error": "Modpack not found."
}
```

**Duplicate version (422):**

```json
{
  "error": "Build version already exists for this modpack."
}
```

**Validation error (422):**

```json
{
  "error": {
    "version": ["The version field is required."],
    "minecraft": ["The minecraft field is required."]
  }
}
```

---

## PUT /api/modpack/{slug}/{version}

Update an existing build.

**Permission required:** `modpacks_manage` + access to the modpack

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug. |
| `version` | string | The current build version string. |

### Request Body

All fields are optional. Only included fields are updated.

| Field | Type | Description |
|-------|------|-------------|
| `version` | string | Build version string. |
| `minecraft` | string | Minecraft version. |
| `forge` | string | Forge version. |
| `is_published` | boolean | Whether the build is visible in the API. |
| `private` | boolean | Restrict to authorized clients. |
| `min_java` | string | Minimum Java version. |
| `min_memory` | integer | Minimum memory in MB. |

### Example Request

```bash
curl -X PUT https://solder.example.com/api/modpack/hexxit/1.0.0 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "is_published": true,
    "forge": "47.2.1"
  }'
```

### Response (200)

```json
{
  "id": 1,
  "modpack_id": 1,
  "version": "1.0.0",
  "minecraft": "1.20.1",
  "forge": "47.2.1",
  "is_published": true,
  "private": false,
  "min_java": "17",
  "min_memory": 2048,
  "created_at": "2026-03-31T12:00:00.000000Z",
  "updated_at": "2026-03-31T12:10:00.000000Z"
}
```

### Error Responses

**Modpack not found (404):**

```json
{
  "error": "Modpack not found."
}
```

**Build not found (404):**

```json
{
  "error": "Build not found."
}
```

---

## DELETE /api/modpack/{slug}/{version}

Delete a build from a modpack.

**Permission required:** `modpacks_manage` + access to the modpack

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug. |
| `version` | string | The build version string. |

### Example Request

```bash
curl -X DELETE https://solder.example.com/api/modpack/hexxit/1.0.0 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "success": "Build deleted."
}
```

### Error Responses

**Modpack not found (404):**

```json
{
  "error": "Modpack not found."
}
```

**Build not found (404):**

```json
{
  "error": "Build not found."
}
```

!!! note
    Deleting a build detaches all mod assignments from that build. The mods and mod versions themselves are not deleted.
