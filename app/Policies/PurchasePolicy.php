<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Purchase;
use Illuminate\Auth\Access\HandlesAuthorization;

class PurchasePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    public function view(User $user, Purchase $purchase): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('owner');
    }

    public function update(User $user, Purchase $purchase): bool
    {
        return $user->hasRole('owner');
    }

    public function delete(User $user, Purchase $purchase): bool
    {
        return $user->hasRole('owner');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('owner');
    }
}
