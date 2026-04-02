# Modpacks

## POST /api/modpack

Create a new modpack.

**Permission required:** `modpacks_create`

### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | Display name. Must be unique. |
| `slug` | string | Yes | URL-friendly identifier. Must be unique; only letters, numbers, dashes, and underscores. |
| `url` | string | No | Website URL for this modpack. Must be a valid URL or null. |
| `hidden` | boolean | No | Hide the modpack from the public API listing. Defaults to `true` if not provided. |
| `private` | boolean | No | Restrict access to authorized clients only. Defaults to `false`. |
| `order` | integer | No | Sort order for display. |

### Example Request

```bash
curl -X POST https://solder.yourdomain.com/api/modpack \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Hexxit",
    "slug": "hexxit",
    "hidden": false,
    "private": false
  }'
```

### Response (201)

```json
{
  "id": 1,
  "name": "Hexxit",
  "slug": "hexxit",
  "recommended": null,
  "latest": null,
  "url": null,
  "icon": false,
  "icon_md5": null,
  "icon_url": null,
  "logo": false,
  "logo_md5": null,
  "logo_url": null,
  "background": false,
  "background_md5": null,
  "background_url": null,
  "order": 0,
  "hidden": false,
  "private": false,
  "created_at": "2026-03-31T12:00:00.000000Z",
  "updated_at": "2026-03-31T12:00:00.000000Z"
}
```

### Error Response (422)

```json
{
  "error": {
    "name": ["The name has already been taken."],
    "slug": ["The slug has already been taken."]
  }
}
```

---

## PUT /api/modpack/{slug}

Update an existing modpack.

**Permission required:** `modpacks_manage` + access to this modpack

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The current modpack slug. |

### Request Body

All fields are optional. Only included fields are updated.

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Display name. Must be unique. |
| `slug` | string | URL-friendly identifier. Must be unique; only letters, numbers, dashes, and underscores. |
| `url` | string | Website URL. Must be a valid URL or null. |
| `hidden` | boolean | Hide from public listing. |
| `private` | boolean | Restrict to authorized clients. |
| `order` | integer | Sort order. |
| `recommended` | string | Recommended build version string. |
| `latest` | string | Latest build version string. |

### Example Request

```bash
curl -X PUT https://solder.yourdomain.com/api/modpack/hexxit \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Hexxit Updated",
    "recommended": "1.0.1",
    "latest": "1.0.2"
  }'
```

### Response (200)

```json
{
  "id": 1,
  "name": "Hexxit Updated",
  "slug": "hexxit",
  "recommended": "1.0.1",
  "latest": "1.0.2",
  "url": null,
  "icon": false,
  "icon_md5": null,
  "icon_url": null,
  "logo": false,
  "logo_md5": null,
  "logo_url": null,
  "background": false,
  "background_md5": null,
  "background_url": null,
  "order": 0,
  "hidden": false,
  "private": false,
  "created_at": "2026-03-31T12:00:00.000000Z",
  "updated_at": "2026-03-31T12:05:00.000000Z"
}
```

### Error Responses

**Modpack not found (404):**

```json
{
  "error": "Modpack not found."
}
```

**Validation error (422):**

```json
{
  "error": {
    "slug": ["The slug has already been taken."]
  }
}
```

---

## DELETE /api/modpack/{slug}

Delete a modpack and all of its builds.

**Permission required:** `modpacks_delete` + access to this modpack

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug. |

### Example Request

```bash
curl -X DELETE https://solder.yourdomain.com/api/modpack/hexxit \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "success": "Modpack deleted."
}
```

### Error Response (404)

```json
{
  "error": "Modpack not found."
}
```

!!! warning
    Deleting a modpack also deletes **all of its builds** and detaches all client associations. This action cannot be undone.
