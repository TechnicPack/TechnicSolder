<?php

use Illuminate\Support\Facades\Facade;

return [

    'aliases' => Facade::defaultAliases()->merge([
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,
        'Redis' => Illuminate\Support\Facades\Redis::class,
    ])->toArray(),

];
