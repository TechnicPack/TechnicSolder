<?php

namespace App\Mods;

class ModProviders
{
    public static function providers()
    {
        $providers = array(
            'modrinth' => \App\Mods\Providers\Modrinth::class
        );

        // If we have a CurseForge api key set add it to the providers list
        if (!empty(config('solder.curseforge_api_key'))) {
            $providers['curseforge'] = \App\Mods\Providers\CurseForge::class;
        }

        return $providers;
    }
}

