<?php

namespace App\Mods;

class ModProviders
{
    public static function providers()
    {
        $providers = array(
            'modrinth' => \App\Mods\Providers\Modrinth::class,
            'fabric' => \App\Mods\Providers\Fabric::class,
            'forge' => \App\Mods\Providers\Forge::class
        );

        return $providers;
    }
}

