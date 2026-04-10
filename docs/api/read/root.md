# Root & Key Verification

## GET /api/

Returns the Solder version and stream. Use this endpoint to verify that your Solder installation is reachable and responding.

### Example Request

```bash
curl https://solder.example.com/api/
```

### Response (200)

```json
{
  "api": "TechnicSolder",
  "version": "1.1.2",
  "stream": "rolling"
}
```

---

## GET /api/verify/{key}

Validate an API key. The Technic Platform calls this endpoint when you link a Solder instance to verify the connection. This endpoint is rate-limited.

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `key` | string | The API key to validate. |

### Example Request

```bash
curl https://solder.example.com/api/verify/YOUR_API_KEY
```

### Response (200)

```json
{
  "valid": "Key validated.",
  "name": "My Key Name"
}
```

### Error Response (403)

```json
{
  "error": "Invalid key provided."
}
```
