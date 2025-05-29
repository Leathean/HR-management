<?php

namespace App\Policies;

use App\Models\Salary;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SalaryPolicy
{
    public function viewAny(User $user): bool
    {
        // Ensure the user has access as either HR or Admin
        return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

    /**
     * Determine if the given user can view a specific leave request.
     */
    public function view(User $user, Salary $model): bool
    {
        // Ensure the user has access as either HR or Admin
        return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }
}
