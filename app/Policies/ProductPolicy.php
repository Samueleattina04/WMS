<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy extends BasePolicy
{
    public function create(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, Product $product): bool
    {
        return $user->isManager();
    }
}
