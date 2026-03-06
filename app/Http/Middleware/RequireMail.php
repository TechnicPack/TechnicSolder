<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireMail
{
    private const GUARDED_ROUTES = [
        'password.request',
        'password.email',
        'password.reset',
        'password.update',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (in_array($request->route()?->getName(), self::GUARDED_ROUTES)) {
            abort_unless(config('solder.mail_enabled'), 404);
        }

        return $next($request);
    }
}
