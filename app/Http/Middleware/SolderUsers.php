<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SolderUsers
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
        $wantedUser = $request->segment(3);
        $action = $request->segment(2);

        $user = $request->user();

        if (!$user) {
            return redirect('dashboard')
                ->with('permission', 'You do not have permission to access this area.');
        }

        $perms = $user->permission;

        if (!$perms->solder_full && !$perms->solder_users) {
            /* This allows the user to edit their own profile */
            if ($action == 'edit') {
                if ($wantedUser != $user->id) {
                    return redirect('dashboard')
                        ->with('permission', 'You do not have permission to access this area.');
                }
            } else {
                return redirect('dashboard')
                    ->with('permission', 'You do not have permission to access this area.');
            }
        }

        return $next($request);
    }
}
