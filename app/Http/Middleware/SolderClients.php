<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;

class SolderClients
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if (! $user) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }
        $perms = $user->permission;
        if (! $perms->solder_full && ! $perms->solder_clients) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
