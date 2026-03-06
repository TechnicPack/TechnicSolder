<?php

namespace App\Policies;

use App\Models\User;

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

    public function update(User $user, User $target): bool
    {
        return $user->permission->solder_users || $user->id === $target->id;
    }

    public function delete(User $user): bool
    {
        return $user->permission->solder_users;
    }
}
