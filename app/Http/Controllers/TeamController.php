<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
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
        
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            $teams = Team::withCount('employees')->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $teams = Team::withCount('employees')
                ->whereIn('id', $assignedTeamIds)
                ->get();
        } else {
            $teams = collect();
        }
        
        $teams = $teams->map(function ($team) use ($currentMonth, $currentYear) {
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

                $team = Team::create([
                    'name' => $request->name,
                ]);
        
                // Audit log for team creation
                AuditLog::logCreated(
                    'teams',
                    $team,
                    "New team created: {$team->name}"
                );

                return redirect()->route('teams.index')->with('success', 'Team created successfully!');
    }

        public function update(Request $request, $id)
        {
            $team = Team::findOrFail($id);
        
            $request->validate([
                'name' => 'required|string|max:255|unique:teams,name,' . $id,
            ]);

            $oldName = $team->name;
            $team->update([
                'name' => $request->name,
            ]);
        
            // Audit log for team update
            AuditLog::logUpdated(
                'teams',
                $team,
                "Team renamed: {$oldName} → {$request->name}",
                ['name' => $oldName],
                ['name' => $request->name]
            );

            return redirect()->route('teams.index')->with('success', 'Team updated successfully!');
        }

        public function destroy($id)
        {
            $team = Team::findOrFail($id);
        
            $teamName = $team->name;
            $employeeCount = $team->employees()->count();
        
            $team->employees()->update(['team_id' => null]);
        
            // Audit log for team deletion
            AuditLog::logDeleted(
                'teams',
                $team,
                "Team deleted: {$teamName} ({$employeeCount} employees unassigned)"
            );
        
            $team->delete();

            return redirect()->route('teams.index')->with('success', 'Team deleted successfully!');
        }

    public function show($id)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $user = auth()->user();
        
        if (!$user->isAdmin()) {
            if ($user->isManager() || $user->isTeamLeader()) {
                $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
                if (!$assignedTeamIds->contains($id)) {
                    abort(403, 'Unauthorized access.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }
        
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

            $team = Team::findOrFail($id);
            $employee = Employee::with('user')->findOrFail($request->employee_id);
            $oldTeamId = $employee->team_id;
            $employee->team_id = $id;
            $employee->save();
        
            // Audit log for employee assignment
            AuditLog::log(
                'assigned',
                'teams',
                "Employee {$employee->user->name} assigned to team {$team->name}",
                $employee,
                ['team_id' => $oldTeamId],
                ['team_id' => $id]
            );

            return redirect()->route('teams.show', $id)->with('success', 'Employee assigned to team successfully!');
        }

        public function removeEmployee($teamId, $employeeId)
        {
            $team = Team::findOrFail($teamId);
            $employee = Employee::with('user')->findOrFail($employeeId);
            $oldTeamId = $employee->team_id;
            $employee->team_id = null;
            $employee->save();
        
            // Audit log for employee removal
            AuditLog::log(
                'removed',
                'teams',
                "Employee {$employee->user->name} removed from team {$team->name}",
                $employee,
                ['team_id' => $oldTeamId],
                ['team_id' => null]
            );

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
            'employees.*.emi_override_amount' => 'nullable|numeric|min:0',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2020|max:2100',
        ]);

        foreach ($request->employees as $employeeId => $data) {
            $employee = Employee::findOrFail($employeeId);
            $deductEmi = isset($data['deduct_emi']) ? true : false;
            
            $updateData = [
                'working_days' => $data['working_days'],
                'deduct_emi' => $deductEmi,
            ];
            
            if (isset($data['emi_override_amount']) && $data['emi_override_amount'] !== '' && $data['emi_override_amount'] !== null) {
                $updateData['emi_override_amount'] = $data['emi_override_amount'];
                $updateData['emi_override_by'] = auth()->id();
                $updateData['emi_override_reason'] = 'Manual adjustment by team leader';
            } else {
                $updateData['emi_override_amount'] = null;
                $updateData['emi_override_by'] = null;
                $updateData['emi_override_reason'] = null;
            }
            
            if (!$deductEmi) {
                $this->handleEmiSkipRollover($employee, $request->month, $request->year);
            }
            
            EmployeeWorkingDay::updateOrCreate(
                [
                    'employee_id' => $employeeId,
                    'month' => $request->month,
                    'year' => $request->year,
                ],
                $updateData
            );
        }

        return redirect()->route('teams.show', $teamId)->with('success', 'Salary settings updated successfully for all team members!');
    }
    
    private function handleEmiSkipRollover(Employee $employee, int $month, int $year)
    {
        $activeLoans = $employee->activeLoans()->get();
        
        foreach ($activeLoans as $loan) {
            $currentMonthStart = \Carbon\Carbon::create($year, $month, 1)->startOfMonth();
            $currentMonthEnd = \Carbon\Carbon::create($year, $month, 1)->endOfMonth();
            
            $pendingPayment = $loan->payments()
                ->where('status', 'pending')
                ->whereBetween('due_date', [$currentMonthStart, $currentMonthEnd])
                ->first();
            
            if ($pendingPayment) {
                $lastPayment = $loan->payments()
                    ->orderBy('installment_number', 'desc')
                    ->first();
                
                $newDueDate = \Carbon\Carbon::parse($lastPayment->due_date)->addMonth();
                $newInstallmentNumber = $lastPayment->installment_number + 1;
                
                $pendingPayment->update([
                    'due_date' => $newDueDate,
                    'installment_number' => $newInstallmentNumber,
                ]);
                
                $loan->increment('total_months');
                $loan->increment('remaining_months');
            }
        }
    }
}
