<?php

use Illuminate\Support\Facades\Broadcast;

// Private channel for user-specific notifications
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Private channel for requests - accessible by admins, managers, team leaders, HR
Broadcast::channel('requests', function ($user) {
    return $user->hasRole(['admin', 'manager', 'team-leader', 'hr']);
});

// Private channel for loans - accessible by admins, accountants, managers
Broadcast::channel('loans', function ($user) {
    return $user->hasRole(['admin', 'accountant', 'manager']);
});

// Private channel for team-specific updates
Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    if ($user->hasRole('admin')) {
        return true;
    }
    
    $employee = $user->employee;
    if (!$employee) {
        return false;
    }
    
    // Check if user is assigned to this team or is a team leader/manager of this team
    return $employee->team_id == $teamId || 
           $employee->teamAssignments()->where('team_id', $teamId)->exists();
});

// Public channel for dashboard updates (general stats)
Broadcast::channel('dashboard', function () {
    return true;
});

// Public channel for employee list updates
Broadcast::channel('employees', function () {
    return true;
});
