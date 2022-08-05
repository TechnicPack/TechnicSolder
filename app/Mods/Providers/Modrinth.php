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
        return array(
            "User-Agent: TechnicPack/TechnicSolder/" . SOLDER_VERSION
        );
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
        $mod->members = static::request("/v2/project/$modId/members");
        return $mod;
    }
    
    protected static function download(string $modId)
    {
        $modData = static::mod($modId);

        $url = "";
        $fileName = "";
        if (count($modData->versions[0]->files) > 1) {
            foreach ($modData->versions[0]->files as $file) {
                if ($file->primary) {
                    $url = $file->url;
                    $fileName = $file->filename;
                    break;
                }
            }
        } else {
            $url = $modData->versions[0]->files[0]->url;
            $fileName = $modData->versions[0]->files[0]->filename;
        }

        $tmpFileName = tempnam(sys_get_temp_dir(), "mod");

        $tmpFile = fopen($tmpFileName, "wb");

        $curl_h = curl_init($url);

        curl_setopt($curl_h, CURLOPT_FILE, $tmpFile);
        curl_setopt($curl_h, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl_h, CURLOPT_HTTPHEADER, static::apiHeaders());

        curl_exec($curl_h);

        curl_close($curl_h);

        fclose($tmpFile);

        return (object) [
            "filePath" => $tmpFileName,
            "fileName" => $fileName,
            "mod" => static::generateModData($modData)
        ];
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

        return $modData;
    }
}
