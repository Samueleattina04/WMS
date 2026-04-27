<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Zone;

class ZonePolicy extends BasePolicy
{
    public function create(User $user): bool { return $user->isAdmin(); }
    public function update(User $user, Zone $zone): bool { return $user->isAdmin(); }
    public function delete(User $user, Zone $zone): bool { return $user->isAdmin(); }
}
