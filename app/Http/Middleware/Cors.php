<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Cors
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $origin = config('solder.cors_allowed_origins', '*');

        $response->header('Access-Control-Allow-Origin', $origin);

        return $response;
    }
}
