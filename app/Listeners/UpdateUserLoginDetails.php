<?php

namespace App\Listeners;

use App\Libraries\UpdateUtils;
use App\Models\User;
use Illuminate\Auth\Events\Login;

class UpdateUserLoginDetails
{
    /**
     * Record the login and refresh the superadmin update banner.
     *
     * Bound to the Login event rather than Fortify::authenticateUsing(), which
     * fires once per credential check: twice on a plain login, and before the
     * second factor is supplied on a 2FA one. The guard fires this only once
     * authentication has fully succeeded, 2FA challenge included.
     */
    public function handle(Login $event): void
    {
        if (! $event->user instanceof User) {
            return;
        }

        $user = $event->user;

        $user->last_ip = request()->ip();
        $user->save();

        // Best-effort banner data behind a 60 minute cache. It reaches out to
        // the GitHub API, so it must not hold up the login response.
        defer(function () use ($user) {
            if ($user->permission->solder_full && UpdateUtils::getUpdateCheck()) {
                cache()->put('update', true, now()->addMinutes(60));
            }
        });
    }
}
