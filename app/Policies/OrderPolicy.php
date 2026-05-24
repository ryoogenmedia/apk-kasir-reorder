<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Order;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'admin', 'cashier']);
    }

    public function view(User $user, Order $order): bool
    {
        return $user->hasAnyRole(['owner', 'admin', 'cashier']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'cashier']);
    }

    public function update(User $user, Order $order): bool
    {
        return $user->hasRole('owner');
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->hasRole('owner');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('owner');
    }
}
