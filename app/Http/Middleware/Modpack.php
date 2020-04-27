<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Modpack
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $modpack = $request->segment(3);
        $user = $request->user();
        if (!$user) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }
        $perms = $user->permission;

        if (empty($modpack)) {
            return redirect('dashboard');
        }

        if (!$perms->solder_full && !in_array($modpack, $perms->modpacks)) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
