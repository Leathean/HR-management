<?php

namespace App\Policies;

use App\Models\User;
use App\Models\schedule;
class SchedulePolicy
{
    public function viewAny(User $user): bool
    {
        // Ensure the user has access as either HR or Admin
        return true;
    }

    /**
     * Determine if the given user can view a specific leave request.
     */
    public function view(User $user, schedule $model): bool
    {
        // Ensure the user has access as either HR or Admin
        return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

       /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
      return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, schedule $salary): bool
    {
    return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, schedule $salary): bool
    {
    return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

}
