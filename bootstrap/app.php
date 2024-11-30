<?php

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

        $middleware->throttleApi();
        $middleware->api(\App\Http\Middleware\Cors::class);

        $middleware->alias([
            'build' => \App\Http\Middleware\Build::class,
            'cors' => \App\Http\Middleware\Cors::class,
            'modpack' => \App\Http\Middleware\Modpack::class,
            'solder_clients' => \App\Http\Middleware\SolderClients::class,
            'solder_keys' => \App\Http\Middleware\SolderKeys::class,
            'solder_modpacks' => \App\Http\Middleware\SolderModpacks::class,
            'solder_mods' => \App\Http\Middleware\SolderMods::class,
            'solder_users' => \App\Http\Middleware\SolderUsers::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
