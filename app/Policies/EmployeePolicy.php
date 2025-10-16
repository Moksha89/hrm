<?php

namespace App\Policies;

use App\Models\Employee;
use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->isManager() || $user->isTeamLeader() || $user->isHR() || $user->isAccountant();
    }

    public function view(User $user, Employee $employee): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->employee && $user->employee->id === $employee->id) {
            return true;
        }

        if ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            return $assignedTeamIds->contains($employee->team_id);
        }

        if ($user->isHR()) {
            return true;
        }

        if ($user->isAccountant()) {
            return true;
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isTeamLeader();
    }

    public function update(User $user, Employee $employee): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            return $assignedTeamIds->contains($employee->team_id);
        }

        if ($user->isHR()) {
            return true;
        }

        return false;
    }

    public function delete(User $user, Employee $employee): bool
    {
        return $user->isAdmin();
    }
}
