<?php

namespace App\Mods;

use App\Models\Mod;
use App\Models\Modversion;
use Illuminate\Support\Str;
use ZipArchive;

abstract class ModProvider
{
    abstract public static function name() : string;
    abstract protected static function apiUrl() : string;
    abstract protected static function apiHeaders() : array;
    abstract public static function search(string $query, int $page = 1) : object;
    abstract public static function mod(string $modId) : object;
    abstract protected static function download(string $modId);

    public static function install(string $modId)
    {
        $downloadData = static::download($modId);

        // Create the mod entry
        $mod = new Mod();
        $mod->name = Str::slug($downloadData->mod->slug);
        $mod->pretty_name = $downloadData->mod->name;
        $mod->author = $downloadData->mod->authors;
        $mod->description = $downloadData->mod->summary;
        $mod->link = $downloadData->mod->websiteUrl;
        $mod->save();

        $slug = $mod->name;

        $zip = new ZipArchive();
        $res = $zip->open($downloadData->filePath, ZipArchive::RDONLY);
        if ($res === false) {
            // TODO Error
            return;
        }

        $forgeData = $zip->getFromName('mcmod.info');
        if ($forgeData === false) {
            // TODO Error
            return;
        }

        $zip->close();

        $version = json_decode($forgeData)[0]->version;

        // Check if the final path isnt a url
        $location = config('solder.repo_location');
        $finalPath = $location."mods/$slug/$slug-$version.zip";
        if (filter_var($finalPath, FILTER_VALIDATE_URL)) {
            // TODO Error
            return;
        }

        // Create the mod dir
        if (!file_exists(dirname($finalPath))) {
            mkdir(dirname($finalPath), 0777, true);
        }

        // Create the final mod zip
        $zip = new ZipArchive();
        $zip->open($finalPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($downloadData->filePath, "mods/" . $downloadData->fileName);
        $zip->close();

        // Add the version
        $ver = new Modversion();
        $ver->mod_id = $mod->id;
        $ver->version = $version;
        $ver->filesize = filesize($finalPath);
        $ver->md5 = md5_file($finalPath);
        $ver->save();

        return redirect('mod/view/'.$mod->id);
    }

    protected static function request(string $url)
    {
        $curl_h = curl_init(static::apiUrl() . $url);

        curl_setopt($curl_h, CURLOPT_HTTPHEADER, static::apiHeaders());

        # do not output, but store to variable
        curl_setopt($curl_h, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl_h);
        return json_decode($response);
    }
}
