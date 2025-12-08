<?php

namespace App\Policies;

use App\Models\Request;
use App\Models\User;

class RequestPolicy
{
    /**
     * Determine whether the user can view any requests.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view requests (filtered by role in controller)
    }

    /**
     * Determine whether the user can view the request.
     */
    public function view(User $user, Request $request): bool
    {
        // Admin, Manager can view any request
        if ($user->hasRole(['admin', 'manager'])) {
            return true;
        }

        // User can view requests they created
        if ($request->requested_by === $user->id) {
            return true;
        }

        // User can view requests for their own employee record
        if ($user->employee && $request->employee_id === $user->employee->id) {
            return true;
        }

        // Team Leader can view requests for employees in their assigned teams
        if ($user->hasRole('team-leader')) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            return $assignedTeamIds->contains($request->employee->team_id);
        }

        return false;
    }

    /**
     * Determine whether the user can create requests.
     */
    public function create(User $user): bool
    {
        return true; // All authenticated users can create requests
    }

    /**
     * Determine whether the user can approve the request.
     */
    public function approve(User $user, Request $request): bool
    {
        // Only pending requests can be approved
        if ($request->status !== 'pending') {
            return false;
        }

        // Admin, Manager can approve any request
        if ($user->hasRole(['admin', 'manager'])) {
            return true;
        }

        // Team Leader can approve requests for employees in their assigned teams
        if ($user->hasRole('team-leader')) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            return $assignedTeamIds->contains($request->employee->team_id);
        }

        return false;
    }

    /**
     * Determine whether the user can reject the request.
     */
    public function reject(User $user, Request $request): bool
    {
        // Same rules as approve
        return $this->approve($user, $request);
    }

    /**
     * Determine whether the user can delete the request.
     */
    public function delete(User $user, Request $request): bool
    {
        // Only admin can delete, and only if request is pending
        return $user->hasRole('admin') && $request->status === 'pending';
    }
}
