<?php

namespace App\Policies;

use App\Models\Slot;
use App\Models\User;

class SlotPolicy extends BasePolicy
{
    public function create(User $user): bool { return $user->isManager(); }
    public function update(User $user, Slot $slot): bool { return $user->isManager(); }
    public function delete(User $user, Slot $slot): bool { return $user->isAdmin(); }
}
