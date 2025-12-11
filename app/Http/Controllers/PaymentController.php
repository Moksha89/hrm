<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Payment;
use App\Models\SalaryPayment;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    public function index()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $pendingLoans = Loan::with(['employee.user', 'employee.team', 'bankAccount', 'processedBy'])
            ->where('status', 'pending')
            ->get()
            ->map(function ($loan) use ($currentMonth, $currentYear) {
                $loan->employee->net_pay = $loan->employee->getNetPay($currentMonth, $currentYear);
                $loan->employee->total_emi = $loan->employee->getTotalMonthlyEmi();
                $loan->employee->final_pay = $loan->employee->getFinalPay($currentMonth, $currentYear);
                return $loan;
            });
        
        $pendingEmis = LoanPayment::with(['loan.employee.user', 'loan.employee.team', 'loan.bankAccount'])
            ->where('status', 'pending')
            ->whereHas('loan', function ($query) {
                $query->where('status', 'active');
            })
            ->where('due_date', '<=', now()->endOfMonth())
            ->orderBy('due_date', 'asc')
            ->get()
            ->map(function ($payment) use ($currentMonth, $currentYear) {
                $payment->loan->employee->net_pay = $payment->loan->employee->getNetPay($currentMonth, $currentYear);
                $payment->loan->employee->total_emi = $payment->loan->employee->getTotalMonthlyEmi();
                $payment->loan->employee->final_pay = $payment->loan->employee->getFinalPay($currentMonth, $currentYear);
                return $payment;
            });
        
        return view('payments.index', compact('pendingLoans', 'pendingEmis', 'currentMonth', 'currentYear'));
    }

    public function approveLoan(Request $request, $loanId)
    {
        $loan = Loan::findOrFail($loanId);
        
        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'This loan has already been processed.');
        }

        $loan->approval_status = 'approved';
        $loan->processed_by = auth()->id();
        $loan->save();
        
        \App\Models\EmployeeActivity::create([
            'employee_id' => $loan->employee_id,
            'activity_type' => 'loan_approved',
            'description' => 'Loan application of ₹' . number_format($loan->total_amount, 2) . ' approved',
            'data' => [
                'loan_id' => $loan->id,
                'amount' => $loan->total_amount,
            ],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('payments.index')->with('success', 'Loan approved successfully! You can now disburse it.');
    }

    public function rejectLoan(Request $request, $loanId)
    {
        $loan = Loan::findOrFail($loanId);
        
        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'This loan has already been processed.');
        }

        $loan->status = 'rejected';
        $loan->approval_status = 'rejected';
        $loan->processed_by = auth()->id();
        $loan->save();
        
        \App\Models\EmployeeActivity::create([
            'employee_id' => $loan->employee_id,
            'activity_type' => 'loan_rejected',
            'description' => 'Loan application of ₹' . number_format($loan->total_amount, 2) . ' rejected',
            'data' => [
                'loan_id' => $loan->id,
                'amount' => $loan->total_amount,
            ],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->route('payments.index')->with('success', 'Loan rejected.');
    }

    public function disburseLoan(Request $request, $loanId)
    {
        if (!auth()->user()->isAccountant() && !auth()->user()->isAdmin()) {
            abort(403, 'Only accountants can disburse payments.');
        }
        
        $validated = $request->validate([
            'utr_number' => 'required|string',
            'notes' => 'nullable|string',
        ]);
        
        $loan = Loan::with(['employee', 'bankAccount'])->findOrFail($loanId);
        
        if ($loan->status !== 'pending' || $loan->approval_status !== 'approved') {
            return redirect()->back()->with('error', 'Loan must be approved before disbursement.');
        }

        DB::transaction(function () use ($loan, $validated) {
            Payment::create([
                'payment_type' => 'loan_disbursement',
                'employee_id' => $loan->employee_id,
                'amount' => $loan->total_amount,
                'loan_id' => $loan->id,
                'bank_account_id' => $loan->bank_account_id,
                'status' => 'completed',
                'approval_status' => 'approved',
                'processed_by' => auth()->id(),
                'transaction_date' => now(),
                'notes' => 'Loan disbursed to employee bank account',
                'utr' => $validated['utr_number'],
                'remarks' => $validated['notes'] ?? null,
            ]);

            $loan->status = 'active';
            $loan->save();
            
            \App\Models\EmployeeActivity::create([
                'employee_id' => $loan->employee_id,
                'activity_type' => 'loan_disbursed',
                'description' => 'Loan of ₹' . number_format($loan->total_amount, 2) . ' disbursed',
                'data' => [
                    'loan_id' => $loan->id,
                    'amount' => $loan->total_amount,
                    'utr' => $validated['utr_number'],
                ],
                'performed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('payments.index')->with('success', 'Loan disbursed successfully!');
    }

    public function collectEmi(Request $request, $loanPaymentId)
    {
        $loanPayment = LoanPayment::with(['loan'])->findOrFail($loanPaymentId);
        
        if ($loanPayment->status === 'paid') {
            return redirect()->back()->with('error', 'This EMI has already been paid.');
        }

        DB::transaction(function () use ($loanPayment, $request) {
            Payment::create([
                'payment_type' => 'emi_collection',
                'employee_id' => $loanPayment->loan->employee_id,
                'amount' => $loanPayment->amount,
                'loan_id' => $loanPayment->loan_id,
                'loan_payment_id' => $loanPayment->id,
                'status' => 'completed',
                'transaction_date' => now(),
                'notes' => $request->notes ?? 'EMI collected from employee salary',
            ]);

            $loanPayment->status = 'paid';
            $loanPayment->paid_date = now();
            $loanPayment->save();

            $loan = $loanPayment->loan;
            $loan->remaining_balance = max(0, $loan->remaining_balance - $loanPayment->amount);
            $loan->remaining_months = $loan->payments()->where('status', 'pending')->count();
            
            if ($loan->remaining_balance == 0) {
                $loan->status = 'paid';
            }
            
            $loan->save();
            
            \App\Models\EmployeeActivity::create([
                'employee_id' => $loanPayment->loan->employee_id,
                'activity_type' => 'emi_collected',
                'description' => 'EMI of ₹' . number_format($loanPayment->amount, 2) . ' collected for loan',
                'data' => [
                    'loan_id' => $loanPayment->loan_id,
                    'amount' => $loanPayment->amount,
                    'installment_number' => $loanPayment->installment_number,
                ],
                'performed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('payments.index')->with('success', 'EMI payment collected successfully!');
    }

    public function salaryTeams()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $teams = Team::withCount(['employees' => function ($query) {
            $query->where('status', 'active');
        }])
        ->with(['employees' => function ($query) use ($currentMonth, $currentYear) {
            $query->where('status', 'active')
                  ->with(['user', 'salaryPayments' => function ($q) use ($currentMonth, $currentYear) {
                      $q->where('month', $currentMonth)->where('year', $currentYear);
                  }]);
        }])
        ->get();
        
        return view('payments.salary-teams', compact('teams', 'currentMonth', 'currentYear'));
    }

    public function salaryTeamEmployees($teamId)
    {
        $team = Team::with(['employees' => function ($query) {
            $query->where('status', 'active')->with(['user', 'bankAccounts']);
        }])->findOrFail($teamId);
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $employees = $team->employees->map(function ($employee) use ($currentMonth, $currentYear) {
            $employee->net_pay = $employee->getNetPay($currentMonth, $currentYear);
            $employee->total_emi = $employee->getTotalMonthlyEmi();
            $employee->final_pay = $employee->getFinalPay($currentMonth, $currentYear);
            $employee->working_days_count = $employee->getWorkingDays($currentMonth, $currentYear);
            $employee->has_salary_payment = $employee->salaryPayments()
                ->where('month', $currentMonth)
                ->where('year', $currentYear)
                ->where('status', 'completed')
                ->exists();
            return $employee;
        });
        
        return view('payments.salary-employees', compact('team', 'employees', 'currentMonth', 'currentYear'));
    }

    public function approveSalary(Request $request, $employeeId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $salaryPayment = SalaryPayment::where('employee_id', $employeeId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();
        
        if (!$salaryPayment) {
            return redirect()->back()->with('error', 'No salary payment found for this employee.');
        }
        
        if ($salaryPayment->status === 'completed') {
            return redirect()->back()->with('error', 'Salary has already been disbursed.');
        }

        $salaryPayment->approval_status = 'approved';
        $salaryPayment->processed_by = auth()->id();
        $salaryPayment->save();
        
        $monthName = date('F', mktime(0, 0, 0, $currentMonth, 1));
        \App\Models\EmployeeActivity::create([
            'employee_id' => $employeeId,
            'activity_type' => 'salary_approved',
            'description' => 'Salary for ' . $monthName . ' ' . $currentYear . ' approved',
            'data' => [
                'month' => $currentMonth,
                'year' => $currentYear,
                'final_pay' => $salaryPayment->final_pay,
            ],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Salary approved! You can now disburse it.');
    }

    public function rejectSalary(Request $request, $employeeId)
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $salaryPayment = SalaryPayment::where('employee_id', $employeeId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();
        
        if (!$salaryPayment) {
            return redirect()->back()->with('error', 'No salary payment found for this employee.');
        }
        
        if ($salaryPayment->status === 'completed') {
            return redirect()->back()->with('error', 'Salary has already been disbursed.');
        }

        $salaryPayment->status = 'rejected';
        $salaryPayment->approval_status = 'rejected';
        $salaryPayment->processed_by = auth()->id();
        $salaryPayment->save();
        
        $monthName = date('F', mktime(0, 0, 0, $currentMonth, 1));
        \App\Models\EmployeeActivity::create([
            'employee_id' => $employeeId,
            'activity_type' => 'salary_rejected',
            'description' => 'Salary for ' . $monthName . ' ' . $currentYear . ' rejected',
            'data' => [
                'month' => $currentMonth,
                'year' => $currentYear,
            ],
            'performed_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Salary payment rejected.');
    }

    public function disburseSalary(Request $request, $employeeId)
    {
        if (!auth()->user()->isAccountant() && !auth()->user()->isAdmin()) {
            abort(403, 'Only accountants can disburse payments.');
        }
        
        $validated = $request->validate([
            'notes' => 'nullable|string',
        ]);
        
        $employee = Employee::with(['bankAccounts'])->findOrFail($employeeId);
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $existingPayment = SalaryPayment::where('employee_id', $employeeId)
            ->where('month', $currentMonth)
            ->where('year', $currentYear)
            ->first();
            
        if ($existingPayment && $existingPayment->status === 'completed') {
            return redirect()->back()->with('error', 'Salary already paid for this month.');
        }
        
        if ($existingPayment && $existingPayment->approval_status !== 'approved') {
            return redirect()->back()->with('error', 'Salary must be approved before disbursement.');
        }
        
        $netPay = $employee->getNetPay($currentMonth, $currentYear);
        $totalEmi = $employee->getTotalMonthlyEmi();
        $finalPay = $employee->getFinalPay($currentMonth, $currentYear);
        $workingDays = $employee->getWorkingDays($currentMonth, $currentYear);
        
        $defaultBankAccount = $employee->bankAccounts()->where('is_default', true)->first();
        
        DB::transaction(function () use ($employee, $currentMonth, $currentYear, $netPay, $totalEmi, $finalPay, $workingDays, $defaultBankAccount, $validated) {
            SalaryPayment::updateOrCreate(
                [
                    'employee_id' => $employee->id,
                    'month' => $currentMonth,
                    'year' => $currentYear,
                ],
                [
                    'gross_salary' => $employee->salary,
                    'working_days' => $workingDays,
                    'net_pay' => $netPay,
                    'total_emi' => $totalEmi,
                    'final_pay' => $finalPay,
                    'bank_account_id' => $defaultBankAccount?->id,
                    'status' => 'completed',
                    'approval_status' => 'approved',
                    'processed_by' => auth()->id(),
                    'payment_date' => now(),
                    'notes' => $validated['notes'] ?? 'Salary disbursed to employee bank account',
                    'utr' => null,
                    'remarks' => null,
                ]
            );
            
            $monthName = date('F', mktime(0, 0, 0, $currentMonth, 1));
            \App\Models\EmployeeActivity::create([
                'employee_id' => $employee->id,
                'activity_type' => 'salary_credited',
                'description' => 'Salary of ₹' . number_format($finalPay, 2) . ' credited for ' . $monthName . ' ' . $currentYear,
                'data' => [
                    'month' => $currentMonth,
                    'year' => $currentYear,
                    'gross_salary' => $employee->salary,
                    'net_pay' => $netPay,
                    'total_emi' => $totalEmi,
                    'final_pay' => $finalPay,
                ],
                'performed_by' => auth()->id(),
            ]);
        });
        
        return redirect()->back()->with('success', 'Salary payment disbursed successfully!');
    }

    public function salaryHistory()
    {
        $payments = SalaryPayment::with(['employee.user', 'employee.team', 'bankAccount'])
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->paginate(50);
        
        return view('payments.salary-history', compact('payments'));
    }

    public function transactionHistory()
    {
        $transactions = Payment::with(['employee.user', 'loan', 'loanPayment'])
            ->orderBy('created_at', 'desc')
            ->paginate(50);
        
        return view('payments.transaction-history', compact('transactions'));
    }

    public function salaries()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $teams = Team::withCount(['employees' => function ($query) {
            $query->where('status', 'active');
        }])
        ->with(['employees' => function ($query) use ($currentMonth, $currentYear) {
            $query->where('status', 'active')
                  ->with(['user', 'salaryPayments' => function ($q) use ($currentMonth, $currentYear) {
                      $q->where('month', $currentMonth)->where('year', $currentYear);
                  }]);
        }])
        ->get();
        
        return view('payments.salaries', compact('teams', 'currentMonth', 'currentYear'));
    }

    public function employees()
    {
        $employees = Employee::with(['user', 'team'])->get();
        
        return view('payments.employees', compact('employees'));
    }

    public function employeeDetail($employeeId)
    {
        $employee = Employee::with([
            'user',
            'team',
            'bankAccounts',
            'loans.payments',
            'salaryPayments' => function($query) {
                $query->orderBy('year', 'desc')->orderBy('month', 'desc')->limit(6);
            }
        ])->findOrFail($employeeId);
        
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        $recentTransactions = Payment::where('employee_id', $employeeId)
            ->with(['loan', 'loanPayment'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
        
        return view('payments.employee-detail', compact('employee', 'currentMonth', 'currentYear', 'recentTransactions'));
    }
}
