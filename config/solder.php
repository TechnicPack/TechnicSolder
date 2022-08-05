<?php

use Illuminate\Support\Str;

return [
    /**
     * Mod Repository Location
     *
     * This is the location of your mod repository. INCLUDE a trailing slash!
     * This can be a URL or an absolute file location.
     *
     **/
    'repo_location' => Str::finish(env('SOLDER_REPO_LOCATION', '/var/www/mods.solder.test/'), '/'),

    /**
     * Mirror Location
     *
     * This is where the launcher will be told to search for your files.
     * INCLUDE a trailing slash! If your repo location is already a URL you
     * can use the same location here.
     *
     **/
    'mirror_url' => Str::finish(env('SOLDER_MIRROR_URL', 'http://mods.solder.test/'), '/'),

    /**
     * MD5 Connect Timeout
     *
     * This is the amount of time (in seconds) Solder will wait before giving up trying to
     * connect to a URL to perform a MD5 checksum.
     *
     **/
    'md5_connect_timeout' => intval(env('SOLDER_MD5_CONNECT_TIMEOUT', '5')),

    /**
     * MD5 Hashing Timeout
     *
     * This is the amount of time (in seconds) Solder will wait before giving up trying to
     * calculate the MD5 checksum.
     *
     **/
    'md5_file_timeout' => intval(env('SOLDER_MD5_FILE_TIMEOUT', '30')),

    /**
     * Disable mod API functionality?
     *
     * Setting this to true will disable the /mod endpoints in the API responses. You usually don't need to
     * mess with this, but you might want to disable the mod API functionalities for privacy reasons.
     */
    'disable_mod_api' => filter_var(env('SOLDER_DISABLE_MOD_API', 'false'), FILTER_VALIDATE_BOOLEAN),

    'curseforge_api_key' => strval(env('SOLDER_CURSEFORGE_API_KEY', '')),

];
