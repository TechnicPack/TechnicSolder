# Utilities

## POST /api/minecraft/refresh

Refresh the cached list of Minecraft versions from Mojang's version manifest.

### Example Request

```bash
curl -X POST https://solder.yourdomain.com/api/minecraft/refresh \
  -H "Authorization: Bearer YOUR_TOKEN"
```

### Response (200)

```json
{
  "success": "Minecraft versions cache refreshed."
}
```

!!! tip
    Call this endpoint after Mojang releases a new Minecraft version to update the version list available in Solder's build creation UI. Under normal circumstances, the cache refreshes automatically and you should not need to call this endpoint manually.
