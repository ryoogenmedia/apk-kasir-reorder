<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    public function view(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('owner');
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasRole('owner');
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasRole('owner');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('owner');
    }
}
