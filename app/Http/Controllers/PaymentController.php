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
        
        $pendingLoans = Loan::with(['employee.user', 'employee.team', 'bankAccount'])
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

    public function disburseLoan(Request $request, $loanId)
    {
        $loan = Loan::with(['employee', 'bankAccount'])->findOrFail($loanId);
        
        if ($loan->status !== 'pending') {
            return redirect()->back()->with('error', 'This loan has already been processed.');
        }

        DB::transaction(function () use ($loan, $request) {
            Payment::create([
                'payment_type' => 'loan_disbursement',
                'employee_id' => $loan->employee_id,
                'amount' => $loan->total_amount,
                'loan_id' => $loan->id,
                'bank_account_id' => $loan->bank_account_id,
                'status' => 'completed',
                'transaction_date' => now(),
                'notes' => $request->notes ?? 'Loan disbursed to employee bank account',
            ]);

            $loan->status = 'active';
            $loan->save();
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

    public function disburseSalary(Request $request, $employeeId)
    {
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
        
        $netPay = $employee->getNetPay($currentMonth, $currentYear);
        $totalEmi = $employee->getTotalMonthlyEmi();
        $finalPay = $employee->getFinalPay($currentMonth, $currentYear);
        $workingDays = $employee->getWorkingDays($currentMonth, $currentYear);
        
        $defaultBankAccount = $employee->bankAccounts()->where('is_default', true)->first();
        
        DB::transaction(function () use ($employee, $currentMonth, $currentYear, $netPay, $totalEmi, $finalPay, $workingDays, $defaultBankAccount, $request) {
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
                    'payment_date' => now(),
                    'notes' => $request->notes ?? 'Salary disbursed to employee bank account',
                ]
            );
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
}
