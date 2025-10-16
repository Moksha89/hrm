<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->isAdmin() || $user->isAccountant()) {
            $teams = Team::withCount('employees')->get();
        } elseif ($user->isManager() || $user->isTeamLeader()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            $teams = Team::withCount('employees')
                ->whereIn('id', $assignedTeamIds)
                ->get();
        } else {
            $teams = collect();
        }
        
        $teams = $teams->map(function ($team) {
            $team->total_loans = $team->employees->sum(function ($employee) {
                return $employee->activeLoans()->sum('remaining_balance');
            });
            $team->total_monthly_emi = $team->employees->sum(function ($employee) {
                return $employee->getTotalMonthlyEmi();
            });
            return $team;
        });
        
        return view('loans.index', compact('teams'));
    }

    public function showTeam($teamId)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isAccountant()) {
            if ($user->isManager() || $user->isTeamLeader()) {
                $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
                if (!$assignedTeamIds->contains($teamId)) {
                    abort(403, 'Unauthorized access.');
                }
            } else {
                abort(403, 'Unauthorized access.');
            }
        }
        
        $team = Team::with(['employees.user', 'employees.loans' => function ($query) {
            $query->where('status', 'active')->where('remaining_balance', '>', 0);
        }])->findOrFail($teamId);
        
        return view('loans.team', compact('team'));
    }

    public function showEmployee($employeeId)
    {
        $employee = Employee::with(['user', 'team', 'loans.payments', 'bankAccounts'])->findOrFail($employeeId);
        
        return view('loans.employee', compact('employee'));
    }

    public function store(Request $request, $employeeId)
    {
        $request->validate([
            'loan_amount' => 'required|numeric|min:1',
            'calculation_type' => 'required|in:months,emi',
            'months' => 'required_if:calculation_type,months|nullable|integer|min:1|max:360',
            'monthly_emi' => 'required_if:calculation_type,emi|nullable|numeric|min:1',
            'bank_account_id' => 'required|exists:bank_accounts,id',
            'start_date' => 'required|date',
        ]);

        $employee = Employee::findOrFail($employeeId);
        
        $loanAmount = $request->loan_amount;
        if ($request->calculation_type === 'months') {
            $months = $request->months;
            $monthlyEmi = round($loanAmount / $months, 2);
        } else {
            $monthlyEmi = $request->monthly_emi;
            $months = ceil($loanAmount / $monthlyEmi);
        }

        DB::transaction(function () use ($employee, $loanAmount, $monthlyEmi, $months, $request) {
            $loan = Loan::create([
                'employee_id' => $employee->id,
                'bank_account_id' => $request->bank_account_id,
                'total_amount' => $loanAmount,
                'monthly_emi' => $monthlyEmi,
                'total_months' => $months,
                'remaining_months' => $months,
                'remaining_balance' => $loanAmount,
                'start_date' => $request->start_date,
                'status' => 'pending',
            ]);

            $startDate = \Carbon\Carbon::parse($request->start_date);
            for ($i = 1; $i <= $months; $i++) {
                $amount = ($i === $months) ? ($loanAmount - ($monthlyEmi * ($months - 1))) : $monthlyEmi;
                
                LoanPayment::create([
                    'loan_id' => $loan->id,
                    'installment_number' => $i,
                    'amount' => $amount,
                    'due_date' => $startDate->copy()->addMonths($i - 1),
                    'status' => 'pending',
                ]);
            }
            
            \App\Models\EmployeeActivity::create([
                'employee_id' => $employee->id,
                'activity_type' => 'loan_created',
                'description' => 'Loan application created for ₹' . number_format($loanAmount, 2) . ' with ' . $months . ' months EMI',
                'data' => [
                    'loan_id' => $loan->id,
                    'amount' => $loanAmount,
                    'monthly_emi' => $monthlyEmi,
                    'total_months' => $months,
                ],
                'performed_by' => auth()->id(),
            ]);
        });

        return redirect()->route('loans.employee', $employeeId)->with('success', 'Loan application created successfully! Pending disbursement.');
    }

    public function activateLoan($loanId)
    {
        $loan = Loan::findOrFail($loanId);
        $loan->status = 'active';
        $loan->save();

        return redirect()->back()->with('success', 'Loan activated successfully!');
    }

    public function markPaymentPaid(Request $request, $paymentId)
    {
        $request->validate([
            'paid_date' => 'required|date',
        ]);

        $payment = LoanPayment::findOrFail($paymentId);
        $payment->status = 'paid';
        $payment->paid_date = $request->paid_date;
        $payment->save();

        $loan = $payment->loan;
        $loan->remaining_balance = max(0, $loan->remaining_balance - $payment->amount);
        $loan->remaining_months = $loan->payments()->where('status', 'pending')->count();
        
        if ($loan->remaining_balance == 0) {
            $loan->status = 'paid';
        }
        
        $loan->save();

        return redirect()->back()->with('success', 'Payment marked as paid!');
    }
}
