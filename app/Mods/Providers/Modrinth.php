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
    
    protected static function apiHeaders() : array
    {
        return array();
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
    
    public static function mod(string $modId) : object
    {
        $mod = static::request("/v2/project/$modId");
        $mod->versions = static::request("/v2/project/$modId/version");
        return $mod;
    }
    
    protected static function download(string $modId)
    {

    }
    
    private static function generateModData($mod)
    {
        $modData = new ImportedModData();

        $modData->id = $mod->project_id;
        $modData->slug = $mod->slug;

        $modData->name = $mod->title;
        $modData->summary = $mod->description;

        $modData->authors = $mod->author;

        $modData->thumbnailUrl = $mod->icon_url;
        $modData->thumbnailDesc = $mod->title;
        $modData->websiteUrl = "https://modrinth.com/mod/" . $mod->slug;

        return $modData;
    }
}
