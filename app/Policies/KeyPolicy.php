<?php

namespace App\Policies;

use App\Models\User;

class KeyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permission->solder_keys;
    }

    public function create(User $user): bool
    {
        return $user->permission->solder_keys;
    }

    public function update(User $user): bool
    {
        return $user->permission->solder_keys;
    }

    public function delete(User $user): bool
    {
        return $user->permission->solder_keys;
    }
}
