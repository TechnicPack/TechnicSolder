<?php

namespace App\Mods\Providers;

use App\Mods\ImportedModData;
use App\Mods\ModProvider;

class Modrinth extends ModProvider
{
    public static function name() : string
    {
        return "Modrinth";
    }

    protected static function apiUrl() : string
    {
        return "https://api.modrinth.com";
    }

    public static function search(string $query, int $page = 1) : object
    {
        $pageSize = 20;
        $offset = ($page - 1) * $pageSize;
        $mods = [];
        $data = static::request("/v2/search?limit=$pageSize&offset=$offset&query=$query");
        foreach ($data->hits as $mod) {
            array_push($mods, static::generateModData($mod));
        }

        return (object) [
            'mods' => $mods,
            'pagination' => (object) [
                'currentPage' => ($data->offset / $pageSize) + 1,
                'totalPages' => intval(ceil($data->total_hits / $pageSize)),
                'totalItems' => $data->total_hits
            ]
        ];
    }
    
    public static function mod(string $modId) : ImportedModData
    {
        $mod = static::request("/v2/project/$modId");
        $mod->versions = static::request("/v2/project/$modId/version");
        $mod->members = static::request("/v2/project/$modId/members");
        return static::generateModData($mod);
    }
    
    private static function generateModData($mod)
    {
        $modData = new ImportedModData();

        $modData->id = property_exists($mod, "project_id") ? $mod->project_id : $mod->id;
        $modData->slug = $mod->slug;

        $modData->name = $mod->title;
        $modData->summary = $mod->description;

        $modData->authors = property_exists($mod, "author") ? $mod->author : implode(", ", array_map(fn($value) => $value->user->name, $mod->members));

        $modData->thumbnailUrl = $mod->icon_url;
        $modData->thumbnailDesc = $mod->title;
        $modData->websiteUrl = "https://modrinth.com/mod/" . $mod->slug;

        // Parse the versions if we were given them
        // We have to make sure we are not parsing the wrong versions
        // so ignore data with `project_id` as thats given from search
        if (!property_exists($mod, "project_id") && property_exists($mod, "versions")) {
            $modData->versions = array();
            foreach ($mod->versions as $version) {
                $primaryFile = $version->files[0];
                if (count($version->files) > 1) {
                    foreach ($version->files as $file) {
                        if ($file->primary) {
                            $primaryFile = $file;
                            break;
                        }
                    }
                }

                $modData->versions[$version->name] = (object) [
                    "url" => $primaryFile->url,
                    "filename" => $primaryFile->filename,
                    "gameVersions" => $version->game_versions
                ];
            }
        }

        return $modData;
    }
}
