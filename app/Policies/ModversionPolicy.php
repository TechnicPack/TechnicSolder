<?php

namespace App\Policies;

use App\Models\User;

class ModversionPolicy
{
    public function create(User $user): bool
    {
        return $user->permission->mods_manage;
    }

    public function delete(User $user): bool
    {
        return $user->permission->mods_manage;
    }
}
