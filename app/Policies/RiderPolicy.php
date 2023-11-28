<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Rider;
use Illuminate\Auth\Access\Response;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RiderPolicy
{
    // use HandlesAuthorization;
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        
        return $user->can('view_any_rider');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, rider $rider): bool
    {
        return $user->can('view_rider');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_rider');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, rider $rider): bool
    {
        return $user->can('update_rider');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, rider $rider): bool
    {
        return $user->can('delete_rider');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, rider $rider): bool
    {
        return $user->can('restore_rider');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, rider $rider): bool
    {
        return $user->can('force_delete_rider');
    }
}
