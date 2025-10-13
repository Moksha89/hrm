<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeWorkingDay;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $teams = Team::withCount('employees')->get()->map(function ($team) use ($currentMonth, $currentYear) {
            $team->total_salary = $team->employees->sum('salary') ?? 0;
            $team->total_loan = $team->employees->sum('loan') ?? 0;
            $team->total_net_pay = $team->employees->sum(function ($employee) use ($currentMonth, $currentYear) {
                return $employee->getNetPay($currentMonth, $currentYear);
            });
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
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $team = Team::with(['employees.user', 'employees.workingDays' => function ($query) use ($currentMonth, $currentYear) {
            $query->where('month', $currentMonth)->where('year', $currentYear);
        }])->findOrFail($id);
        
        $totalSalary = $team->employees->sum('salary');
        $totalLoan = $team->employees->sum('loan');
        $totalNetPay = $team->employees->sum(function ($employee) use ($currentMonth, $currentYear) {
            return $employee->getNetPay($currentMonth, $currentYear);
        });
        $totalEmi = $team->employees->sum(function ($employee) {
            return $employee->getTotalMonthlyEmi();
        });
        $totalFinalPay = $team->employees->sum(function ($employee) use ($currentMonth, $currentYear) {
            return $employee->getFinalPay($currentMonth, $currentYear);
        });
        
        $unassignedEmployees = Employee::with('user')
            ->whereNull('team_id')
            ->where('status', 'active')
            ->get();

        return view('teams.show', compact('team', 'totalSalary', 'totalLoan', 'totalNetPay', 'totalEmi', 'totalFinalPay', 'unassignedEmployees', 'currentMonth', 'currentYear'));
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

    public function updateWorkingDays(Request $request, $teamId)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'working_days' => 'required|integer|min:0|max:31',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        $employee = Employee::findOrFail($request->employee_id);
        
        EmployeeWorkingDay::updateOrCreate(
            [
                'employee_id' => $request->employee_id,
                'month' => $request->month,
                'year' => $request->year,
            ],
            ['working_days' => $request->working_days]
        );

        return redirect()->route('teams.show', $teamId)->with('success', 'Working days updated successfully!');
    }

    public function updateBulkSalarySettings(Request $request, $teamId)
    {
        $request->validate([
            'employees' => 'required|array',
            'employees.*.working_days' => 'required|integer|min:0|max:31',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        foreach ($request->employees as $employeeId => $data) {
            $employee = Employee::findOrFail($employeeId);
            
            EmployeeWorkingDay::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'month' => $request->month,
                    'year' => $request->year,
                ],
                [
                    'working_days' => $data['working_days'],
                    'deduct_emi' => isset($data['deduct_emi']) ? true : false,
                ]
            );
        }

        return redirect()->route('teams.show', $teamId)->with('success', 'Salary settings updated successfully for all team members!');
    }
}
