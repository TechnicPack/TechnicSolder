<?php

namespace App\Libraries;

use Github\Client;
use RuntimeException;

class UpdateUtils
{
    private static Client $githubClient;

    public static function getGithubClient(): Client
    {
        return self::$githubClient ??= new Client();
    }

    public static function getUpdateCheck($forceRefresh = false): bool
    {
        if ($forceRefresh) {
            cache()->forget('update:github:tags');
        }

        $allVersions = self::getAllVersions();

        if (! array_key_exists('error', $allVersions)) {
            if (version_compare(self::getLatestVersion()['name'], SOLDER_VERSION, '>')) {
                return true;
            }
        }

        return false;
    }

    public static function getLatestVersion()
    {
        $allVersions = self::getAllVersions();
        if (array_key_exists('error', $allVersions)) {
            return $allVersions;
        }

        return $allVersions[0];
    }

    public static function getAllVersions()
    {
        $client = self::getGithubClient();

        try {
            return cache()->remember('update:github:tags', now()->addMinutes(60), function () use ($client) {
                return $client->api('repo')->tags('technicpack', 'technicsolder');
            });
        } catch (RuntimeException $e) {
            return ['error' => 'Unable to fetch versions from Github: '.$e->getMessage()];
        }
    }

    public static function getLatestChangeLog($branch = 'master')
    {
        $client = self::getGithubClient();

        try {
            return cache()->remember('update:github:changelog:'.$branch, now()->addMinutes(60), function () use ($client, $branch) {
                return $client->api('repo')->commits()->all('technicpack', 'technicsolder', ['sha' => $branch]);
            });
        } catch (RuntimeException $e) {
            return ['error' => 'Unable to fetch changelog from Github: '.$e->getMessage()];
        }
    }
}
