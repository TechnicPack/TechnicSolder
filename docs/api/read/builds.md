# Builds

## GET /api/modpack/{slug}/{version}

Show a specific build of a modpack. Visibility follows the same rules as `GET /api/modpack/{slug}`: hidden modpacks' builds are accessible by slug without authentication; private modpacks' builds require authentication that grants access. Individual builds marked private additionally require modpack-level access. Unpublished builds return 404 regardless of authentication.

The `mods` array uses natural, case-insensitive mod-name ordering, with ascending mod-version IDs breaking ties. This also applies with `include=mods`.

### Path Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `slug` | string | The modpack slug. |
| `version` | string | The build version string. |

### Query Parameters

| Parameter | Type | Description |
|-----------|------|-------------|
| `cid` | string | Client UUID. Grants access if the client is associated with this modpack. |
| `k` | string | API key. Grants full access regardless of modpack visibility. |
| `include` | string | Set to `mods` to include additional mod metadata in the response. |

### Example Request

```bash
curl https://solder.example.com/api/modpack/hexxit/1.0.0
```

### Response (200)

```json
{
  "id": 1,
  "minecraft": "1.12.2",
  "java": "1.8",
  "java_runtime": null,
  "memory": 2048,
  "forge": "14.23.5.2847",
  "mods": [
    {
      "id": 1,
      "name": "rei-minimap",
      "version": "1.0.0",
      "md5": "abc123def456",
      "filesize": 5242880,
      "url": "https://mods.example.com/mods/rei-minimap/rei-minimap-1.0.0.zip"
    }
  ]
}
```

### Response with `include=mods` (200)

```bash
curl https://solder.example.com/api/modpack/hexxit/1.0.0?include=mods
```

When `include=mods` is set, each mod object includes additional metadata fields:

```json
{
  "id": 1,
  "minecraft": "1.12.2",
  "java": "1.8",
  "java_runtime": null,
  "memory": 2048,
  "forge": "14.23.5.2847",
  "mods": [
    {
      "id": 1,
      "name": "rei-minimap",
      "version": "1.0.0",
      "md5": "abc123def456",
      "filesize": 5242880,
      "url": "https://mods.example.com/mods/rei-minimap/rei-minimap-1.0.0.zip",
      "pretty_name": "Rei's Minimap",
      "author": "ReiFNSK",
      "description": "A minimap mod",
      "link": "https://example.com"
    }
  ]
}
```

### Mojang Java Runtime Override

`java_runtime` is a nullable Mojang runtime component name, separate from the
minimum Java requirement in `java`. For example, `"java_runtime": "java-runtime-delta"`
selects Mojang's Java 21 runtime for this build.

The build UI shows this setting only when `SOLDER_ADVANCED_MODE=true` (default:
`false`). The flag does not affect API reads, API writes, or saved overrides.
Editing a build without submitting the runtime field preserves its current value.

| Component | Java version |
|-----------|--------------|
| `jre-legacy` | 8 |
| `java-runtime-alpha` | 16 |
| `java-runtime-beta` | 17 |
| `java-runtime-gamma` | 17 |
| `java-runtime-delta` | 21 |
| `java-runtime-epsilon` | 25 |

These are the supported release components from [Mojang's runtime catalog](https://launchermeta.mojang.com/v1/products/java-runtime/2ec0cc96c44e5a76b9c8b7c39df7210883d12871/all.json).
The value selects a component, not a pinned patch release, executable path, or custom download URL.
Availability depends on the player's OS and architecture.

**Launcher integration contract:** when Mojang Java is enabled, a non-null
override takes precedence over the runtime selected by Minecraft metadata or
loader/version patches. `null` (or an absent field from older Solder servers)
leaves normal runtime selection unchanged. This does not enable Mojang Java or
replace a player's custom Java selection. The existing `java` and `memory`
requirements retain their meaning.

Requires a launcher version with `java_runtime` support. LauncherV3 applies the
contract above; older launcher versions ignore the field.

### Error Responses

**Modpack not found (404):**

```json
{
  "error": "Modpack does not exist"
}
```

**Build not found (404):**

```json
{
  "error": "Build does not exist"
}
```
