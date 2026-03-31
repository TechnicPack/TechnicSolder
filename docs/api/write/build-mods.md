# Build Mods

Manage which mods (and which versions of those mods) are included in a build.

## POST /api/modpack/{slug}/{version}/mod

Add a mod to a build.

**Permission required:** `modpacks_manage` + access to the modpack

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug. |
| `version` | string | The build version string. |

### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `mod_slug` | string | Yes | The slug (name) of the mod to add. |
| `mod_version` | string | Yes | The version string of the mod to add. |

### Example Request

```bash
curl -X POST https://solder.yourdomain.com/api/modpack/hexxit/1.0.0/mod \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "mod_slug": "buildcraft",
    "mod_version": "7.1.0"
  }'
```

### Response (201)

```json
{
  "success": "Mod added to build."
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

**Mod not found (404):**

```json
{
  "error": "Mod not found."
}
```

**Mod version not found (404):**

```json
{
  "error": "Mod version not found."
}
```

**Exact version already in build (422):**

```json
{
  "error": "Mod version already in build."
}
```

**Different version of same mod already in build (422):**

```json
{
  "error": "Another version of this mod is already in the build. Use PUT to update it."
}
```

---

## PUT /api/modpack/{slug}/{version}/mod/{modSlug}

Update which version of a mod is included in a build. Use this when the build already contains a different version of the same mod and you want to swap it.

**Permission required:** `modpacks_manage` + access to the modpack

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug. |
| `version` | string | The build version string. |
| `modSlug` | string | The slug (name) of the mod to update. |

### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `mod_version` | string | Yes | The new version string for this mod. |

### Example Request

```bash
curl -X PUT https://solder.yourdomain.com/api/modpack/hexxit/1.0.0/mod/buildcraft \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "mod_version": "7.2.0"
  }'
```

### Response (200)

```json
{
  "success": "Mod version updated in build."
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

**Mod not found (404):**

```json
{
  "error": "Mod not found."
}
```

**Mod version not found (404):**

```json
{
  "error": "Mod version not found."
}
```

**Mod not currently in build (404):**

```json
{
  "error": "Mod not in this build."
}
```

---

## DELETE /api/modpack/{slug}/{version}/mod/{modSlug}

Remove a mod from a build.

**Permission required:** `modpacks_manage` + access to the modpack

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug. |
| `version` | string | The build version string. |
| `modSlug` | string | The slug (name) of the mod to remove. |

### Example Request

```bash
curl -X DELETE https://solder.yourdomain.com/api/modpack/hexxit/1.0.0/mod/buildcraft \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "success": "Mod removed from build."
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

**Mod not found (404):**

```json
{
  "error": "Mod not found."
}
```

**Mod not in build (404):**

```json
{
  "error": "Mod not in this build."
}
```
