<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class Build
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $buildId = $request->segment(3);
        $build = \App\Build::find($buildId);

        if (empty($build))
            return Redirect::to('dashboard');

        $modpack = $build->modpack;

        $user = Auth::user();
        if (!$user) {
            return Redirect::to('dashboard')
                ->with('permission','You do not have permission to access this area.');
        }
        $perms = $user->permission;

        if (!$perms->solder_full && !in_array($modpack->id, $perms->modpacks))
        {
            return Redirect::to('dashboard')
                ->with('permission','You do not have permission to access this area.');
        }

        return $next($request);
    }
}
