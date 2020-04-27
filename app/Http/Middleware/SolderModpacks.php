<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SolderModpacks
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
        $check = '';
        switch ($request->segment(2)) {
            case 'create':
                $check = 'modpacks_create';
                break;
            case 'delete':
                $check = 'modpacks_delete';
                break;
            default:
                $check = 'modpacks_manage';
                break;
        }
        $user = $request->user();
        if (!$user) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }
        $perms = $user->permission;
        if (!$perms->solder_full && !$perms->{$check}) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
}
