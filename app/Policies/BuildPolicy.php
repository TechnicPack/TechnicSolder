<?php

namespace App\Policies;

use App\Models\Modpack;
use App\Models\User;

class BuildPolicy
{
    public function create(User $user, Modpack $modpack): bool
    {
        return $user->permission->modpacks_manage
            && $user->permission->canAccessModpack($modpack->id);
    }

    public function update(User $user, Modpack $modpack): bool
    {
        return $user->permission->modpacks_manage
            && $user->permission->canAccessModpack($modpack->id);
    }

    public function delete(User $user, Modpack $modpack): bool
    {
        return $user->permission->modpacks_manage
            && $user->permission->canAccessModpack($modpack->id);
    }
}
