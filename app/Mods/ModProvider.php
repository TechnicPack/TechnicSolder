<?php

namespace App\Mods;

use App\Models\Mod;
use App\Models\Modversion;
use App\Mods\ImportedModData;
use Illuminate\Support\Str;
use ZipArchive;

abstract class ModProvider
{
    abstract public static function name() : string;
    abstract protected static function apiUrl() : string;
    abstract protected static function apiHeaders() : array;
    abstract public static function search(string $query, int $page = 1) : object;
    abstract public static function mod(string $modId) : ImportedModData;

    private static function installVersion(int $modId, string $slug, ImportedModData $modData, string $version)
    {
        $url = $modData->versions[$version]->url;
        $fileName = $modData->versions[$version]->filename;

        // Create a temp file to download to
        $tmpFileName = tempnam(sys_get_temp_dir(), "mod");

        // Download the file
        $tmpFile = fopen($tmpFileName, "wb");
        $curl_h = curl_init($url);
        curl_setopt($curl_h, CURLOPT_FILE, $tmpFile);
        curl_setopt($curl_h, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl_h, CURLOPT_HTTPHEADER, static::apiHeaders());
        curl_exec($curl_h);
        curl_close($curl_h);
        fclose($tmpFile);

        // Open the downloaded mod zip file
        $zip = new ZipArchive();
        $res = $zip->open($tmpFileName, ZipArchive::RDONLY);
        if ($res === false) {
            unlink($tmpFileName);
            return ["mod_corrupt" => "Unable to open mod file for version $version, its likely corrupt"];
        }

        $version = "";

        // Try load the version from forge
        $forgeData = $zip->getFromName('mcmod.info');
        if ($forgeData !== false) {
            $version = json_decode($forgeData)[0]->version;
        }

        // Try load the version from fabric
        $fabricData = $zip->getFromName('fabric.mod.json');
        if ($fabricData !== false) {
            $version = json_decode($fabricData)->version;
        }

        // Try load the version from rift
        $riftData = $zip->getFromName('riftmod.json');
        if ($riftData !== false) {
            $version = json_decode($riftData)->version;
        }

        $zip->close();

        // Make sure we have been given a version
        if (empty($version)) {
            unlink($tmpFileName);
            return ["version_missing" => "Unable to detect version number for $version"];
        }

        // Check if the version already exists for the mod
        if (Modversion::where([
            'mod_id' => $modId,
            'version' => $version,
        ])->count() > 0) {
            unlink($tmpFileName);
            return ["version_exists" => "$version already exists"];
        }

        // Check if the final path isnt a url
        $location = config('solder.repo_location');
        $finalPath = $location."mods/$slug/$slug-$version.zip";
        if (filter_var($finalPath, FILTER_VALIDATE_URL)) {
            unlink($tmpFileName);
            return ["remote_repo" => "Mod repo in a remote location so unable to download $version"];
        }

        // Create the mod dir
        if (!file_exists(dirname($finalPath))) {
            mkdir(dirname($finalPath), 0777, true);
        }

        // Create the final mod zip
        $zip = new ZipArchive();
        $zip->open($finalPath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
        $zip->addFile($tmpFileName, "mods/" . $fileName);
        $zip->close();

        // Add the version to the db
        $ver = new Modversion();
        $ver->mod_id = $modId;
        $ver->version = $version;
        $ver->filesize = filesize($finalPath);
        $ver->md5 = md5_file($finalPath);
        $ver->save();
    }

    public static function install(string $modId, array $versions)
    {
        $modData = static::mod($modId);
        $response = (object) [
            "success" => true,
            "id" => -1,
            "errors" => array()
        ];

        $slug = Str::slug($modData->slug);

        $mod = Mod::where('name', $slug)->first();
        if (empty($mod)) {
            // Create the mod entry
            $mod = new Mod();
            $mod->name = $slug;
            $mod->pretty_name = $modData->name;
            $mod->author = $modData->authors;
            $mod->description = $modData->summary;
            $mod->link = $modData->websiteUrl;
            $mod->save();
        }

        $response->id = $mod->id;

        foreach ($versions as $version) {
            $error = static::installVersion($mod->id, $slug, $modData, $version);
            if (!empty($error)) {
                array_push($response->errors, $error);
            }
        }

        return $response;
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
