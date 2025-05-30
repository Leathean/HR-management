<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Employee;
class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        // Ensure the user has access as either HR or Admin
        return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

    /**
     * Determine if the given user can view a specific leave request.
     */
    public function view(User $user, Employee $model): bool
    {
        // Ensure the user has access as either HR or Admin
        return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }
}
