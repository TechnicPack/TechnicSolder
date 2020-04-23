<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class SolderUsers
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
        $wantedUser = $request->segment(3);
        $action = $request->segment(2);

        if (!Auth::check()) {
            return Redirect::to('dashboard')
                ->with('permission','You do not have permission to access this area.');
        }

        $user = Auth::user();
        $perms = $user->permission;

        if (!$perms->solder_full && !$perms->solder_users)
        {
            /* This allows the user to edit their own profile */
            if ($action == 'edit'){
                if ($wantedUser != $user->id){
                    return Redirect::to('dashboard')
                        ->with('permission','You do not have permission to access this area.');
                }
            }
            else
            {
                return Redirect::to('dashboard')
                    ->with('permission','You do not have permission to access this area.');
            }
        }

        return $next($request);
    }
}
