<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SolderMods
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
        switch($request->segment(2)){
            case 'create':
                $check = 'mods_create';
                break;
            case 'delete':
                $check = 'mods_delete';
                break;
            case 'modify':
            case 'view':
            case 'list':
            case 'add-version':
            case 'rehash':
            case 'delete-version':
                $check = 'mods_manage';
                break;
            default:
                return Redirect::to('mod/list');
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
