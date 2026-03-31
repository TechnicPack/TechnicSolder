# Clients

## POST /api/client

Create a new client.

**Permission required:** `solder_clients`

### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | Human-readable client name. Must be unique. |
| `uuid` | string | Yes | Unique client identifier used by the Technic Launcher. Must be unique. |

### Example Request

```bash
curl -X POST https://solder.yourdomain.com/api/client \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Public Client",
    "uuid": "a1b2c3d4-e5f6-7890-abcd-ef1234567890"
  }'
```

### Response (201)

```json
{
  "id": 1,
  "name": "Public Client",
  "uuid": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "created_at": "2026-03-31T12:00:00.000000Z",
  "updated_at": "2026-03-31T12:00:00.000000Z"
}
```

### Error Response (422)

```json
{
  "error": {
    "name": ["The name has already been taken."],
    "uuid": ["The uuid has already been taken."]
  }
}
```

---

## PUT /api/client/{uuid}

Update an existing client.

**Permission required:** `solder_clients`

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `uuid` | string | The client's unique identifier. |

### Request Body

All fields are optional. Only included fields are updated.

| Field | Type | Description |
|-------|------|-------------|
| `name` | string | New display name for the client. |
| `modpacks` | array | Array of modpack IDs this client can access. **Replaces** the entire list (sync, not append). |

### Example Request

```bash
curl -X PUT https://solder.yourdomain.com/api/client/a1b2c3d4-e5f6-7890-abcd-ef1234567890 \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Beta Testers",
    "modpacks": [1, 3, 7]
  }'
```

### Response (200)

```json
{
  "id": 1,
  "name": "Beta Testers",
  "uuid": "a1b2c3d4-e5f6-7890-abcd-ef1234567890",
  "created_at": "2026-03-31T12:00:00.000000Z",
  "updated_at": "2026-03-31T12:05:00.000000Z"
}
```

### Error Responses

**Client not found (404):**

```json
{
  "error": "Client not found."
}
```

**Validation error (422):**

```json
{
  "error": {
    "modpacks.0": ["The selected modpacks.0 is invalid."]
  }
}
```

!!! note
    The `modpacks` field performs a **sync** operation -- it replaces the client's entire modpack access list with the provided IDs. To remove all modpack access, pass an empty array `[]`.

---

## DELETE /api/client/{uuid}

Delete a client.

**Permission required:** `solder_clients`

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `uuid` | string | The client's unique identifier. |

### Example Request

```bash
curl -X DELETE https://solder.yourdomain.com/api/client/a1b2c3d4-e5f6-7890-abcd-ef1234567890 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "success": "Client deleted."
}
```

### Error Response (404)

```json
{
  "error": "Client not found."
}
```

!!! warning
    Deleting a client removes all of its modpack access associations. This action cannot be undone.
