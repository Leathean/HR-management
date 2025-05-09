<?php

namespace App\Policies;

use App\Models\ejob;
use App\Models\User;

class ejobPolicy
{
    public function viewAny(User $user): bool
    {
        // Ensure the user has access as either HR or Admin
        return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

    /**
     * Determine if the given user can view a specific leave request.
     */
    public function view(User $user, ejob $model): bool
    {
        // Ensure the user has access as either HR or Admin
        return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }
}
