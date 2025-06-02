<?php

namespace App\Policies;

use App\Models\DeductionAttendanceRule;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class DeductionAttendanceRulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return in_array($user->ACCESS, ['HR', 'ADMIN']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DeductionAttendanceRule $deductionAttendanceRule): bool
    {
      return in_array($user->ACCESS, ['HR', 'ADMIN']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DeductionAttendanceRule $deductionAttendanceRule): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DeductionAttendanceRule $deductionAttendanceRule): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DeductionAttendanceRule $deductionAttendanceRule): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DeductionAttendanceRule $deductionAttendanceRule): bool
    {
        return false;
    }
}
