<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewNavigationMenu(User $user)
    {
        // Logic to check if the user has a specific role that allows access to the navigation menu
        dd($user->isSuperAdmin() ? true: null);
        return $user->hasRole('dadmin'); // Change 'admin' to the role that should see the menu
        
    }
}
