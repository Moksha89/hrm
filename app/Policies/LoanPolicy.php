<?php

namespace App\Policies;

use App\Models\Loan;
use App\Models\User;

class LoanPolicy
{
    /**
     * Determine whether the user can view any loans.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'team-leader', 'accountant']);
    }

    /**
     * Determine whether the user can view the loan.
     */
    public function view(User $user, Loan $loan): bool
    {
        // Admin, Accountant can view any loan
        if ($user->hasRole(['admin', 'accountant'])) {
            return true;
        }

        // User can view their own loans
        if ($user->employee && $user->employee->id === $loan->employee_id) {
            return true;
        }

        // Manager/Team Leader can view loans for employees in their assigned teams
        if ($user->hasRole(['manager', 'team-leader'])) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            return $assignedTeamIds->contains($loan->employee->team_id);
        }

        return false;
    }

    /**
     * Determine whether the user can create loans.
     */
    public function create(User $user): bool
    {
        return $user->hasRole(['admin', 'manager', 'team-leader', 'accountant']);
    }

    /**
     * Determine whether the user can activate the loan.
     */
    public function activate(User $user, Loan $loan): bool
    {
        return $user->hasRole(['admin', 'accountant']);
    }

    /**
     * Determine whether the user can collect payment on the loan.
     */
    public function collectPayment(User $user, Loan $loan): bool
    {
        return $user->hasRole(['admin', 'accountant']);
    }

    /**
     * Determine whether the user can delete the loan.
     */
    public function delete(User $user, Loan $loan): bool
    {
        // Only admin can delete, and only if loan is pending
        return $user->hasRole('admin') && $loan->status === 'pending';
    }
}
