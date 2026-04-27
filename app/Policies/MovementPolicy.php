<?php

namespace App\Policies;

use App\Models\Movement;
use App\Models\User;

class MovementPolicy extends BasePolicy
{
    public function create(User $user): bool
    {
        return $user->isWarehouse();
    }

    public function update(User $user, Movement $movement): bool
    {
        return false; // Movements are immutable
    }

    public function delete(User $user, Movement $movement): bool
    {
        return false; // Movements cannot be deleted
    }
}
