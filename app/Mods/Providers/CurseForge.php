<?php

namespace App\Mods\Providers;

use App\Mods\ImportedModData;
use App\Mods\ModProvider;

class CurseForge extends ModProvider
{
    public static function name() : string
	{
		return "CurseForge";
	}

	protected static function apiUrl() : string
	{
		return "https://api.curseforge.com";
	}
	
	protected static function apiHeaders() : array
	{
		return array(
			'x-api-key: $2a$10$GGwXrmysP7HXg2yEUFGAuOmTRapITv0ZwYalmWeQDyyarWXh3uwme'
		);
	}

    public static function search(string $query, int $page = 1) : object
	{
		$pageSize = 20;
		$offset = ($page - 1) * $pageSize;
        $mods = [];
		$data = static::request("/v1/mods/search?gameid=432&classId=6&sortOrder=desc&pageSize=$pageSize&index=$offset&searchFilter=$query");
		foreach ($data->data as $mod) {
            array_push($mods, static::generateModData($mod));
        }

        return (object) [
            'mods' => $mods,
            'pagination' => (object) [
                'currentPage' => ($data->pagination->index / $pageSize) + 1,
                'totalPages' => ceil($data->pagination->totalCount / $pageSize),
                'totalItems' => $data->pagination->	totalCount
            ]
        ];
	}
	
    public static function mod(string $modId) : object
	{
		return static::request("/v1/mods/$modId")->data;
	}
	
    protected static function download(string $modId)
	{

	}
	
	private static function generateModData($mod)
	{
		$modData = new ImportedModData();

		$modData->id = strval($mod->id);
		$modData->slug = $mod->slug;

		$modData->name = $mod->name;
		$modData->summary = $mod->summary;

		$authors = [];
        foreach ($mod->authors as $author) {
            array_push($authors, $author->name);
        }
        $modData->authors = implode(", ", $authors);

		$modData->thumbnailUrl = $mod->logo->thumbnailUrl;
		$modData->thumbnailDesc = empty($mod->logo->description) ? $mod->name : $mod->logo->description;
		$modData->websiteUrl = $mod->links->websiteUrl;

		return $modData;
	}
}
