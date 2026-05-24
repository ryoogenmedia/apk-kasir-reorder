<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Supplier;
use Illuminate\Auth\Access\HandlesAuthorization;

class SupplierPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->hasRole('admin');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasRole('admin');
    }
}
