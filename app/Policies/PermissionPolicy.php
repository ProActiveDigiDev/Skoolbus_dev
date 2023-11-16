<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PermissionPolicy
{
    use HandlesAuthorization;

    public function viewNavigationMenu(User $user)
    {
        // Logic to check if the user has a specific permission that allows access to the navigation menu
        return $user->hasPermissionTo('view-navigation-menu'); // Change 'view-navigation-menu' to the permission name
    }
}
