<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Warehouse;

class WarehousePolicy extends BasePolicy
{
    public function create(User $user): bool { return $user->isAdmin(); }
    public function update(User $user, Warehouse $warehouse): bool { return $user->isAdmin(); }
    public function delete(User $user, Warehouse $warehouse): bool { return $user->isAdmin(); }
}
