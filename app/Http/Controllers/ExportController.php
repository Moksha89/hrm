<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Loan;
use App\Models\SalaryPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function employees(Request $request)
    {
        $this->authorize('viewAny', Employee::class);
        
        $user = auth()->user();
        
        if ($user->isAdmin() || $user->isHR() || $user->isAccountant()) {
            $employees = Employee::with(['user', 'team'])->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $employees = Employee::with(['user', 'team'])
                ->whereIn('team_id', $assignedTeamIds)
                ->get();
        } else {
            $employees = collect();
        }
        
        $headers = ['ID', 'Name', 'Mobile', 'Email', 'Team', 'Salary', 'Status', 'Date of Joining'];
        
        $callback = function() use ($employees, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->id,
                    $employee->user->name,
                    $employee->user->mobile,
                    $employee->user->email,
                    $employee->team?->name ?? 'No Team',
                    $employee->salary,
                    $employee->status,
                    $employee->date_of_joining?->format('Y-m-d'),
                ]);
            }
            
            fclose($file);
        };
        
        $filename = 'employees_' . date('Y-m-d_His') . '.csv';
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    public function loans(Request $request)
    {
        $user = auth()->user();
        
        if ($user->isAdmin() || $user->isAccountant()) {
            $loans = Loan::with(['employee.user', 'employee.team'])->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $loans = Loan::with(['employee.user', 'employee.team'])
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->get();
        } else {
            $loans = collect();
        }
        
        $headers = ['ID', 'Employee', 'Team', 'Total Amount', 'Monthly EMI', 'Remaining Balance', 'Remaining Months', 'Status', 'Start Date'];
        
        $callback = function() use ($loans, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            foreach ($loans as $loan) {
                fputcsv($file, [
                    $loan->id,
                    $loan->employee->user->name,
                    $loan->employee->team?->name ?? 'No Team',
                    $loan->total_amount,
                    $loan->monthly_emi,
                    $loan->remaining_balance,
                    $loan->remaining_months,
                    $loan->status,
                    $loan->start_date?->format('Y-m-d'),
                ]);
            }
            
            fclose($file);
        };
        
        $filename = 'loans_' . date('Y-m-d_His') . '.csv';
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    public function salaryHistory(Request $request)
    {
        $user = auth()->user();
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        
        if ($user->isAdmin() || $user->isAccountant()) {
            $payments = SalaryPayment::with(['employee.user', 'employee.team'])
                ->where('month', $month)
                ->where('year', $year)
                ->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $payments = SalaryPayment::with(['employee.user', 'employee.team'])
                ->where('month', $month)
                ->where('year', $year)
                ->whereHas('employee', function ($query) use ($assignedTeamIds) {
                    $query->whereIn('team_id', $assignedTeamIds);
                })
                ->get();
        } else {
            $payments = collect();
        }
        
        $headers = ['ID', 'Employee', 'Team', 'Month', 'Year', 'Base Salary', 'Working Days', 'Net Pay', 'EMI Deduction', 'Final Pay', 'Status', 'UTR'];
        
        $callback = function() use ($payments, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            
            foreach ($payments as $payment) {
                fputcsv($file, [
                    $payment->id,
                    $payment->employee->user->name,
                    $payment->employee->team?->name ?? 'No Team',
                    $payment->month,
                    $payment->year,
                    $payment->base_salary,
                    $payment->working_days,
                    $payment->net_pay,
                    $payment->emi_deduction,
                    $payment->final_pay,
                    $payment->status,
                    $payment->utr,
                ]);
            }
            
            fclose($file);
        };
        
        $filename = 'salary_history_' . $month . '_' . $year . '_' . date('Y-m-d_His') . '.csv';
        
        return Response::stream($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
