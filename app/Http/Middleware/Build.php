<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class Build
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
        $buildId = $request->segment(3);
        $build = \App\Build::find($buildId);

        if (empty($build)) {
            return redirect('dashboard');
        }

        $modpack = $build->modpack;

        $user = Auth::user();
        if (!$user) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }
        $perms = $user->permission;

        if (!$perms->solder_full && !in_array($modpack->id, $perms->modpacks)) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
