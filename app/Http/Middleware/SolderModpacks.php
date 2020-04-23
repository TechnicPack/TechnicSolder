<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SolderModpacks
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
        $check = '';
        switch($request->segment(2)){
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
        $user = Auth::user();
        if (!$user) {
            return Redirect::to('dashboard')
                ->with('permission','You do not have permission to access this area.');
        }
        $perms = $user->permission;
        if (!$perms->solder_full && !$perms->{$check})
        {
            return Redirect::to('dashboard')
                ->with('permission','You do not have permission to access this area.');
        }

        return $next($request);
    }
}
