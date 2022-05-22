<?php

namespace App\Libraries;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MinecraftUtils
{
    public static function getMinecraftVersions($forceRefresh = false)
    {
        if ($forceRefresh) {
            Cache::forget('minecraftversions');
        }

        return Cache::remember('minecraftversions', now()->addHours(3), fn() => self::fetchVersions());
    }

    public static function fetchVersions()
    {
        $client = UrlUtils::getGuzzleClient();

        // Try to get them from Technic
        try {
            $response = $client->get('https://www.technicpack.net/api/minecraft');
            if ($response->getStatusCode() === 200) {
                // Decode the JSON content of the reply
                $versions = json_decode((string) $response->getBody(), true);

                // Sort versions, from most recent to oldest
                krsort($versions, SORT_NATURAL);

                return $versions;
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to fetch Minecraft versions from Technic: ' . $e->getMessage());
        }

        // If getting them from Technic fails, we get it directly from Mojang
        try {
            $response = $client->get('https://launchermeta.mojang.com/mc/game/version_manifest_v2.json');
            if ($response->getStatusCode() === 200) {
                $json = json_decode((string) $response->getBody(), true);

                // The format is ['1.12.2' => ['version' => '1.12.2'], ...]
                $versions = [];

                foreach ($json as $mojangVersion) {
                    if ($mojangVersion['type'] !== 'release') {
                        continue;
                    }

                    $mcVersion = $mojangVersion['id'];

                    $versions[$mcVersion] = ['version' => $mcVersion];
                }

                krsort($versions, SORT_NATURAL);

                return $versions;
            }
        } catch (GuzzleException $e) {
            Log::error('Failed to fetch Minecraft versions from Mojang: ' . $e->getMessage());
        }

        Log::error('Failed to fetch Minecraft versions');

        return [];
    }
}
