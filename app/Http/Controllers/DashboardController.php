<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Team;
use App\Models\Request;
use App\Models\SalaryPayment;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isAdmin()) {
            $totalEmployees = Employee::where('status', 'active')->count();
            $activeTeams = Team::count();
            $pendingRequests = Request::where('status', 'pending')->count();
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $totalPayments = SalaryPayment::where('month', $currentMonth)
                ->where('year', $currentYear)
                ->where('status', 'completed')
                ->sum('final_pay');
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $totalEmployees = Employee::whereIn('team_id', $assignedTeamIds)
                ->where('status', 'active')->count();
            $activeTeams = Team::whereIn('id', $assignedTeamIds)->count();
            $pendingRequests = Request::whereHas('employee', function ($query) use ($assignedTeamIds) {
                $query->whereIn('team_id', $assignedTeamIds);
            })->where('status', 'pending')->count();
            $totalPayments = 0;
        } elseif ($user->isAccountant()) {
            $totalEmployees = Employee::where('status', 'active')->count();
            $activeTeams = Team::count();
            $pendingRequests = 0;
            $currentMonth = now()->month;
            $currentYear = now()->year;
            $totalPayments = SalaryPayment::where('month', $currentMonth)
                ->where('year', $currentYear)
                ->where('status', 'completed')
                ->sum('final_pay');
        } else {
            $totalEmployees = 0;
            $activeTeams = 0;
            $pendingRequests = Request::where('employee_id', $user->employee?->id)
                ->where('status', 'pending')->count();
            $totalPayments = 0;
        }
        
        return view('dashboard', compact('totalEmployees', 'activeTeams', 'pendingRequests', 'totalPayments'));
    }
}
