<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use Closure;
use Illuminate\Http\Request;

class Build
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $buildId = $request->segment(3);
        $build = \App\Models\Build::find($buildId);

        if (empty($build)) {
            return redirect('dashboard');
        }

        $modpack = $build->modpack;

        $user = $request->user();
        if (! $user) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }
        $perms = $user->permission;

        if (! $perms->solder_full && ! in_array($modpack->id, $perms->modpacks)) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
