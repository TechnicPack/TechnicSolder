<?php

use App\Http\Middleware\Build;
use App\Http\Middleware\Cors;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\Modpack;
use App\Http\Middleware\SolderClients;
use App\Http\Middleware\SolderKeys;
use App\Http\Middleware\SolderModpacks;
use App\Http\Middleware\SolderMods;
use App\Http\Middleware\SolderUsers;
use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

        $middleware->web([HandleInertiaRequests::class]);
        $middleware->throttleApi();
        $middleware->api(Cors::class);

        $middleware->alias([
            'build' => Build::class,
            'cors' => Cors::class,
            'modpack' => Modpack::class,
            'solder_clients' => SolderClients::class,
            'solder_keys' => SolderKeys::class,
            'solder_modpacks' => SolderModpacks::class,
            'solder_mods' => SolderMods::class,
            'solder_users' => SolderUsers::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
