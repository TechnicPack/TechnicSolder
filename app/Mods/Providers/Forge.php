<?php

namespace App\Mods\Providers;

use App\Mods\ImportedModData;
use App\Mods\ModProvider;
use SimpleXMLElement;

class Forge extends ModProvider
{
    public static function name() : string
    {
        return "Forge";
    }

    protected static function apiUrl() : string
    {
        return "https://maven.minecraftforge.net/net/minecraftforge/forge/";
    }

    protected static function zipFolder() : string
    {
        return "bin";
    }

    protected static function useRawVersion() : bool
    {
        return true;
    }

    private static function getVersionsMap()
    {
        $data = new SimpleXMLElement(static::request("/maven-metadata.xml", true));
        $versionsMap = array();
        foreach ($data->versioning->versions->version as $mod) {
            $mcVersion = explode("-", $mod)[0];
            $modVersion = explode("-", $mod)[1];
            if (!array_key_exists($mcVersion, $versionsMap)) {
                $versionsMap[$mcVersion] = array();
            }
            array_push($versionsMap[$mcVersion], $modVersion);
        }

        return $versionsMap;
    }

    public static function search(string $query, int $page = 1) : object
    {
        $versionsMap = static::getVersionsMap();

        $mods = [];
        foreach ($versionsMap as $mcVersion => $versions) {
            array_push($mods, static::generateModData($mcVersion, $versions));
        }

        return (object) [
            'mods' => $mods,
            'pagination' => (object) [
                'currentPage' => 1,
                'totalPages' => 1,
                'totalItems' => count($mods)
            ]
        ];
    }
    
    public static function mod(string $modId) : ImportedModData
    {
        $versionsMap = static::getVersionsMap();
        return static::generateModData($modId, $versionsMap[$modId]);
    }
    
    private static function generateModData($mod, $versions)
    {
        $modData = new ImportedModData();

        $modData->id = $mod;
        $modData->slug = "forge";

        // For the mod library entry
        $modData->name = "Minecraft Forge";
        $modData->summary = "Minecraft Forge is a common open source API allowing a broad range of mods to work cooperatively together. Is allows many mods to be created without them editing the main Minecraft Code.";

        // For showing to the user on the import page
        $modData->displayName = "Minecraft Forge - MC $mod";
        $modData->displaySummary = "Forge Mod Loader for Minecraft $mod";

        $modData->authors = "LexManos";

        $modData->thumbnailUrl = "https://files.minecraftforge.net/static/images/logo.svg";
        $modData->thumbnailDesc = "Forge";
        $modData->websiteUrl = "https://files.minecraftforge.net/";

        $modData->versions = array();
        foreach ($versions as $version) {
            $modData->versions["$mod-$version"] = (object) [
                "url" => static::apiUrl() . "/$mod-$version/forge-$mod-$version-installer.jar",
                "filename" => "modpack.jar",
                "gameVersions" => [$mod]
            ];
        }

        return $modData;
    }
}
