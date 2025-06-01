<?php

namespace App\Policies;

use App\Models\Payroll;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class PayrollPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
       return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Payroll $salary): bool
    {
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
    public function update(User $user, Payroll $salary): bool
    {
    return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Payroll $salary): bool
    {
    return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Payroll $salary): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Payroll $salary): bool
    {
        return false;
    }
}
