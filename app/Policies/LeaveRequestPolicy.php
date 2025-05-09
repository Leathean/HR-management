<?php
namespace App\Policies;

use App\Models\User;
use App\Models\LeaveRequest;

class LeaveRequestPolicy
{
    /**
     * Determine if the given user can view any leave requests.
     */
    public function viewAny(User $user): bool
    {
        // Ensure the user has access as either HR or Admin
        return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN'|| $user->ACCESS === 'EMPLOYEE';
    }

    /**
     * Determine if the given user can view a specific leave request.
     */
    public function view(User $user, LeaveRequest $model): bool
    {
        // Ensure the user has access as either HR or Admin
        return $user->ACCESS === 'HR' || $user->ACCESS === 'ADMIN'|| $user->ACCESS === 'EMPLOYEE';
    }

    public function create(User $user): bool
{
    return true;
}

}
