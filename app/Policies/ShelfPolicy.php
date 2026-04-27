<?php

namespace App\Policies;

use App\Models\Shelf;
use App\Models\User;

class ShelfPolicy extends BasePolicy
{
    public function create(User $user): bool { return $user->isManager(); }
    public function update(User $user, Shelf $shelf): bool { return $user->isManager(); }
    public function delete(User $user, Shelf $shelf): bool { return $user->isAdmin(); }
}
