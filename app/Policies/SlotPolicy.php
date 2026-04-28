<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class SlotPolicy extends BasePolicy
{
    public function create(User $user): bool { return $user->isManager(); }
    public function update(User $user, Model $model): bool { return $user->isManager(); }
    public function delete(User $user, Model $model): bool { return $user->isAdmin(); }
}
