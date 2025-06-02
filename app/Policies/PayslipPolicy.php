<?php

namespace App\Policies;

use App\Models\Payslip;
use App\Models\User;

class PayslipPolicy
{
    public function viewAny(User $user): bool
    {
        // HR and ADMIN can view all, EMPLOYEE only their own
        return in_array($user->ACCESS, ['HR', 'ADMIN', 'EMPLOYEE']);
    }

    public function view(User $user, Payslip $payslip): bool
    {
        if (in_array($user->ACCESS, ['HR', 'ADMIN'])) {
            return true;
        }

        // Employee can only view their own payslip
        return $user->employee?->id === $payslip->payroll->employees_id;
    }

    public function create(User $user): bool
    {
        return in_array($user->ACCESS, ['HR', 'ADMIN']);
    }

    public function update(User $user, Payslip $payslip): bool
    {
        return in_array($user->ACCESS, ['HR', 'ADMIN']);
    }

    public function delete(User $user, Payslip $payslip): bool
    {
        return in_array($user->ACCESS, ['HR', 'ADMIN']);
    }
}
