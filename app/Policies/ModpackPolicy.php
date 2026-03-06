<?php

namespace App\Policies;

use App\Models\Modpack;
use App\Models\User;

class ModpackPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permission->modpacks_manage;
    }

    public function create(User $user): bool
    {
        return $user->permission->modpacks_create;
    }

    public function update(User $user, Modpack $modpack): bool
    {
        return $user->permission->modpacks_manage
            && $user->permission->canAccessModpack($modpack->id);
    }

    public function delete(User $user, Modpack $modpack): bool
    {
        return $user->permission->modpacks_delete
            && $user->permission->canAccessModpack($modpack->id);
    }
}
