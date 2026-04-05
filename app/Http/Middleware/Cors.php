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

        $allowed = config('solder.cors_allowed_origins', '*');

        if ($allowed === '*') {
            $response->header('Access-Control-Allow-Origin', '*');

            return $response;
        }

        $requestOrigin = $request->header('Origin');
        if ($requestOrigin) {
            $origins = array_map('trim', explode(',', $allowed));
            if (in_array($requestOrigin, $origins, true)) {
                $response->header('Access-Control-Allow-Origin', $requestOrigin);
            }
        }

        $response->header('Vary', 'Origin');

        return $response;
    }
}
