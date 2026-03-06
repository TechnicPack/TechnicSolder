<?php

namespace App\Policies;

use App\Models\Mod;
use App\Models\User;

class ModPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permission->mods_manage;
    }

    public function create(User $user): bool
    {
        return $user->permission->mods_create;
    }

    public function update(User $user, Mod $mod): bool
    {
        return $user->permission->mods_manage;
    }

    public function delete(User $user, Mod $mod): bool
    {
        return $user->permission->mods_delete;
    }
}
