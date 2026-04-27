<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

abstract class BasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->is_active;
    }

    public function view(User $user, Model $model): bool
    {
        return $user->is_active;
    }

    public function create(User $user): bool
    {
        return $user->isWarehouse();
    }

    public function update(User $user, Model $model): bool
    {
        return $user->isWarehouse();
    }

    public function delete(User $user, Model $model): bool
    {
        return $user->isManager();
    }
}
