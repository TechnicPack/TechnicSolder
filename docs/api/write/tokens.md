# Tokens

## GET /api/token

List all tokens belonging to the authenticated user.

### Example Request

```bash
curl https://solder.example.com/api/token \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "tokens": [
    {
      "id": 1,
      "name": "CI Deploy Token",
      "last_used_at": "2026-03-31T12:00:00.000000Z",
      "created_at": "2026-03-31T10:00:00.000000Z"
    },
    {
      "id": 2,
      "name": "Local Dev Token",
      "last_used_at": null,
      "created_at": "2026-03-31T11:00:00.000000Z"
    }
  ]
}
```

!!! note
    Token values are never returned in list responses. If you lose a token's plaintext value, revoke it and create a new one.

---

## POST /api/token

Create a new API token for the authenticated user.

### Request Body

| Field | Type | Required | Description |
|-------|------|----------|-------------|
| `name` | string | Yes | A descriptive name for the token (max 255 characters). |

### Example Request

```bash
curl -X POST https://solder.example.com/api/token \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "CI Deploy Token"
  }'
```

### Response (201)

```json
{
  "token": {
    "id": 1,
    "name": "CI Deploy Token",
    "plaintext": "1|abc123def456...",
    "created_at": "2026-03-31T12:00:00.000000Z"
  }
}
```

!!! warning
    The `plaintext` value is only returned once, at creation time. Store it securely -- you will not be able to retrieve it again.

### Error Response (422)

```json
{
  "error": {
    "name": ["The name field is required."]
  }
}
```

---

## DELETE /api/token/{tokenId}

Revoke a token. You can only revoke tokens that belong to the authenticated user.

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `tokenId` | integer | The ID of the token to revoke. |

### Example Request

```bash
curl -X DELETE https://solder.example.com/api/token/1 \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "success": "Token revoked."
}
```

### Error Response (404)

```json
{
  "error": "Token not found."
}
```

!!! note
    Revoking a token takes effect immediately. Any requests using the revoked token will receive a 401 Unauthorized response.
