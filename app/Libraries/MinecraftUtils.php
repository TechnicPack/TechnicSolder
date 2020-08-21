<?php

namespace App\Libraries;

use Illuminate\Support\Facades\Cache;

class MinecraftUtils
{

    public static function getMinecraft($manual = false)
    {
        $response = [];

        if ($manual) {
            Cache::forget('minecraftversions');
        } else {
            if (!$manual && Cache::has('minecraftversions')) {
                $response = Cache::get('minecraftversions');
            }
        }

        $response = self::getVersions();

        return $response;
    }

    public static function getVersions()
    {
        $response = [];

        if (UrlUtils::checkRemoteFile('https://www.technicpack.net/api/minecraft')['success']) {
            $response = UrlUtils::get_url_contents('https://www.technicpack.net/api/minecraft');
            if ($response['success']) {
                $response = json_decode($response['data'], true);
                krsort($response, SORT_NATURAL);
                Cache::put('minecraftversions', $response, now()->addMinutes(180));
                return $response;
            }
        }

        if (UrlUtils::checkRemoteFile('https://launchermeta.mojang.com/mc/game/version_manifest.json')['success']) {
            $response = UrlUtils::get_url_contents('https://launchermeta.mojang.com/mc/game/version_manifest.json');
            if ($response['success']) {
                $mojangResponse = json_decode($response['data'], true);
                $versions = [];

                foreach ($mojangResponse['versions'] as $mojangVersion) {
                    if ($mojangVersion['type'] !== 'release') {
                        continue;
                    }
                    $mcVersion = $mojangVersion['id'];
                    $versions[$mcVersion] = ['version' => $mcVersion];
                }

                krsort($versions, SORT_NATURAL);
                Cache::put('minecraftversions', $versions, now()->addMinutes(180));
                return $versions;
            }
        }

        return $response;
    }
}
