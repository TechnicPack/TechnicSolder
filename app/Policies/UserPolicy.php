<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserPermission;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permission->solder_users;
    }

    public function create(User $user): bool
    {
        return $user->permission->solder_users;
    }

    /**
     * A user may always edit their own profile; the controller clamps which
     * permissions a non-superadmin can actually change. Otherwise the actor
     * must be able to manage the target. solder_full is handled by the
     * Gate::before in AppServiceProvider, so it never reaches here.
     */
    public function update(User $user, User $target): bool
    {
        return $user->id === $target->id || $this->canManage($user, $target);
    }

    public function delete(User $user, User $target): bool
    {
        return $this->canManage($user, $target);
    }

    /**
     * Whether a non-superadmin manager may act on $target. They must hold
     * solder_users, and $target's permissions must be a subset of their own —
     * so a delegate can never edit, delete, or reset 2FA for a more-privileged
     * account (and never for a solder_full superadmin). An equal peer is a
     * subset of itself, so peer management is allowed.
     */
    private function canManage(User $actor, User $target): bool
    {
        $actorPerm = $actor->permission;
        $targetPerm = $target->permission;

        if (! $actorPerm || ! $targetPerm || ! $actorPerm->solder_users) {
            return false;
        }

        if ($targetPerm->solder_full) {
            return false;
        }

        foreach (UserPermission::permissionFlags() as $flag) {
            if ($targetPerm->{$flag} && ! $actorPerm->{$flag}) {
                return false;
            }
        }

        foreach ($targetPerm->modpacks as $modpackId) {
            if (! $actorPerm->canAccessModpack((int) $modpackId)) {
                return false;
            }
        }

        return true;
    }
}
