<?php

namespace App\Policies;

use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->permission->solder_clients;
    }

    public function create(User $user): bool
    {
        return $user->permission->solder_clients;
    }

    public function update(User $user): bool
    {
        return $user->permission->solder_clients;
    }

    public function delete(User $user): bool
    {
        return $user->permission->solder_clients;
    }
}
