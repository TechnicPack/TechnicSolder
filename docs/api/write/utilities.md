# Utilities

## POST /api/minecraft/refresh

Refresh the cached list of Minecraft versions. Fetches from Technic's API first, falling back to Mojang's version manifest.

### Example Request

```bash
curl -X POST https://solder.example.com/api/minecraft/refresh \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "success": "Minecraft versions cache refreshed."
}
```

!!! tip
    Call this endpoint after a new Minecraft version is released to update the version list available in Solder's build creation UI. Under normal circumstances, the cache refreshes automatically and you should not need to call this endpoint manually.
