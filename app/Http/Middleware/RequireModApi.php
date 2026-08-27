<?php

namespace App\Http\Middleware;

use App\Models\Mod;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireModApi
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('solder.disable_mod_api')) {
            return $next($request);
        }

        $user = auth('sanctum')->user();

        if ($user instanceof User && $user->can('viewAny', Mod::class)) {
            return $next($request);
        }

        return response()->json(['error' => 'Mod API has been disabled'], 404);
    }
}
