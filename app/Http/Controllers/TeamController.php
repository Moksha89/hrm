<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $teams = Team::withCount('employees')->get()->map(function ($team) {
            $team->total_salary = $team->employees->sum('salary') ?? 0;
            $team->total_loan = $team->employees->sum('loan') ?? 0;
            return $team;
        });

        return view('teams.index', compact('teams'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:teams,name',
        ]);

        Team::create([
            'name' => $request->name,
        ]);

        return redirect()->route('teams.index')->with('success', 'Team created successfully!');
    }

    public function show($id)
    {
        $team = Team::with(['employees.user'])->findOrFail($id);
        
        $totalSalary = $team->employees->sum('salary');
        $totalLoan = $team->employees->sum('loan');
        
        $unassignedEmployees = Employee::with('user')
            ->whereNull('team_id')
            ->get();

        return view('teams.show', compact('team', 'totalSalary', 'totalLoan', 'unassignedEmployees'));
    }

    public function assignEmployee(Request $request, $id)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        $employee->team_id = $id;
        $employee->save();

        return redirect()->route('teams.show', $id)->with('success', 'Employee assigned to team successfully!');
    }

    public function removeEmployee($teamId, $employeeId)
    {
        $employee = Employee::findOrFail($employeeId);
        $employee->team_id = null;
        $employee->save();

        return redirect()->route('teams.show', $teamId)->with('success', 'Employee removed from team successfully!');
    }
}
