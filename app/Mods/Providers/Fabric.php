<?php

namespace App\Mods\Providers;

use App\Mods\ImportedModData;
use App\Mods\ModProvider;

class Fabric extends ModProvider
{
    public static function name() : string
    {
        return "Fabric";
    }

    protected static function apiUrl() : string
    {
        return "https://meta.fabricmc.net";
    }

    protected static function zipFolder() : string
    {
        return "bin";
    }

    protected static function useRawVersion() : bool
    {
        return true;
    }

    public static function search(string $query, int $page = 1) : object
    {
        $mods = [];
        $data = static::request("/v2/versions/game");
        foreach ($data as $mod) {
            if ($mod->stable) {
                array_push($mods, static::generateModData($mod));
            }
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
        $data = static::request("/v2/versions/game"); // TODO remove this request?
        $foundMod = (object) [];
        foreach ($data as $mod) {
            if ($mod->version == $modId) {
                $foundMod = $mod;
            }
        }
        
        $mod = $foundMod;
        $mod->versions = static::request("/v2/versions/loader");
        return static::generateModData($mod);
    }
    
    private static function generateModData($mod)
    {
        $modData = new ImportedModData();

        $modData->id = $mod->version;
        $modData->slug = "fabric-loader";

        // For the mod library entry
        $modData->name = "Fabric Loader";
        $modData->summary = "Fabric Loader for Minecraft";

        // For showing to the user on the import page
        $modData->displayName = "Fabric Loader $mod->version";
        $modData->displaySummary = "Fabric Loader for Minecraft $mod->version";

        $modData->authors = "FabricMC";

        $modData->thumbnailUrl = "https://fabricmc.net/assets/logo.png";
        $modData->thumbnailDesc = "FabricMC";
        $modData->websiteUrl = "https://fabricmc.net/";

        // Parse the versions if we were given them
        if (property_exists($mod, "versions")) {
            $modData->versions = array();
            foreach ($mod->versions as $version) {
                $modData->versions["$mod->version-$version->version"] = (object) [
                    "url" => static::apiUrl() . "/v2/versions/loader/$mod->version/$version->version/profile/json",
                    "filename" => "version.json",
                    "gameVersions" => [$mod->version]
                ];
            }
        }

        return $modData;
    }
}
