<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;

class EmployeeProfileController extends Controller
{
    public function show()
    {
        $user = auth()->user();
        
        if (!$user->employee) {
            abort(404, 'Employee profile not found.');
        }
        
        $employee = Employee::with([
            'user',
            'team',
            'bankAccounts',
            'documents',
            'loans.loanPayments',
            'loans.bankAccount',
            'salaryPayments',
            'workingDays',
            'activities.performedBy'
        ])->findOrFail($user->employee->id);
        
        $totalLoanAmount = $employee->loans->sum('total_amount');
        $totalRemainingBalance = $employee->loans->sum('remaining_balance');
        $totalMonthlyEmi = $employee->loans->sum('monthly_emi');
        
        $totalSalaryPaid = $employee->salaryPayments->where('status', 'completed')->sum('amount');
        $totalWorkingDays = $employee->workingDays->sum('days_worked');
        
        return view('profile.show', compact(
            'employee',
            'totalLoanAmount',
            'totalRemainingBalance',
            'totalMonthlyEmi',
            'totalSalaryPaid',
            'totalWorkingDays'
        ));
    }
}
