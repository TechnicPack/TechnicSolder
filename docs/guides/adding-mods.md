# Adding Mods

This guide covers how to prepare mod packages, organize them in the repository, and register them in Solder.

## Repository Structure

Solder serves mod files from a repository directory defined by the `SOLDER_REPO_LOCATION` setting (see [Configuration](../getting-started/configuration.md)). Inside that directory, each mod gets its own folder named after its **slug**:

```
[repo_location]/
  mods/
    damage-indicators/
      damage-indicators-1.2.3.zip
      damage-indicators-1.2.4.zip
    buildcraft/
      buildcraft-7.1.23.zip
    forge/
      forge-1971.zip
```

!!! note
    Slugs are always lowercase and use hyphens instead of spaces. The slug you choose here must match the **Mod Slug** you set in the Solder GUI.

## Zip File Format

Each mod version is packaged as a zip file following this naming convention:

```
[mod-slug]-[version].zip
```

For example, a mod with slug `damage-indicators` at version `1.2.4` becomes `damage-indicators-1.2.4.zip`.

When the Technic Launcher installs a mod, it extracts the zip contents into the root of the modpack directory. This means the internal folder structure of the zip must mirror the directory layout expected inside a Minecraft instance.

## Package Types

Different types of content require different internal zip structures.

=== "Regular Mod"

    A standard mod jar goes inside a `mods/` directory:

    ```
    damage-indicators-1.2.4.zip
      mods/
        DamageIndicators-1.2.4.jar
    ```

=== "Config Pack"

    Configuration files go inside a `config/` directory. You can include files at other paths too -- everything extracts to the modpack root:

    ```
    configpack-1.0.zip
      config/
        configA.cfg
        configB.cfg
      config.txt
      server.dat
    ```

=== "Forge Loader"

    Forge is packaged with the universal or installer jar renamed to `modpack.jar` inside a `bin/` directory:

    ```
    forge-1971.zip
      bin/
        modpack.jar
    ```

    Use the Forge **universal** jar for Minecraft up to 1.12.2, and the Forge **installer** jar for Minecraft 1.13+.

=== "Fabric Loader"

    Fabric uses a `version.json` file inside a `bin/` directory. Follow the [FabricMC wiki](https://fabricmc.net/wiki/tutorial:technic_modpacks) to generate this file:

    ```
    fabric-loader-1.0.zip
      bin/
        version.json
    ```

!!! warning
    Every mod package must contain the correct internal directory structure. If the zip is missing the expected folders (e.g. `mods/` for a regular mod), the Technic Launcher will not install it correctly.

## Adding a Mod via the GUI

1. In Solder, navigate to **Mod Library > Add Mod**.
2. Enter the **Mod Name** (e.g. `Damage Indicators`).
3. Verify the **Mod Slug** matches the folder name in your repository (e.g. `damage-indicators`).
4. Click **Add Mod**.
5. On the mod's page, go to the **Versions** tab.
6. Enter the version string in the **Version** field (e.g. `1.2.4`). This must match the version portion of the zip filename.
7. Click **Add Version**, then click **Verify** to generate the MD5 checksum.

!!! tip
    The **Verify** button connects to your mod repository and computes an MD5 hash of the zip file. If verification fails, double-check that the zip file exists at the expected path and that `SOLDER_REPO_LOCATION` and `SOLDER_MIRROR_URL` are configured correctly.

## Using the Write API

Mods and mod versions can also be managed programmatically through the write API. This is useful for CI/CD pipelines or scripts that automate mod updates. See the [Mods API reference](../api/write/mods.md) for details.
