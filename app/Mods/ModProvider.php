<?php

namespace App\Mods;

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
        static::download($modId);
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
