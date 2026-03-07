<?php

use App\Providers\AppServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(AppServiceProvider::HOME);

        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
        $middleware->web(\App\Http\Middleware\RequireMail::class);

        $middleware->throttleApi();
        $middleware->api(prepend: [
            \App\Http\Middleware\ForceJsonResponse::class,
        ]);
        $middleware->api(\App\Http\Middleware\Cors::class);

        $middleware->alias([
            'cors' => \App\Http\Middleware\Cors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (AccessDeniedHttpException $e, $request) {
            if (! $request->expectsJson()) {
                return redirect('dashboard')
                    ->with('permission', 'You do not have permission to access this area.');
            }
        });
    })->create();
