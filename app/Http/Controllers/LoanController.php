<?php

namespace App\Http\Controllers;

use App\Events\DashboardUpdated;
use App\Events\LoanStatusChanged;
use App\Models\AuditLog;
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
        $loan = Loan::with('employee')->findOrFail($loanId);
        $loan->status = 'active';
        $loan->save();

        // Broadcast real-time events
        event(new LoanStatusChanged($loan));
        event(new DashboardUpdated('loan_activated', ['loan_id' => $loan->id]));

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
        $loan->load('employee');
        $loan->remaining_balance = max(0, $loan->remaining_balance - $payment->amount);
        $loan->remaining_months = $loan->payments()->where('status', 'pending')->count();
        
        if ($loan->remaining_balance == 0) {
            $loan->status = 'paid';
        }
        
        $loan->save();

        // Broadcast real-time events
        event(new LoanStatusChanged($loan));
        event(new DashboardUpdated('payment_collected', ['loan_id' => $loan->id, 'payment_id' => $payment->id]));

        return redirect()->back()->with('success', 'Payment marked as paid!');
    }

    /**
     * Show loan management page for a specific loan
     */
    public function manage($loanId)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isManager() && !$user->isTeamLeader()) {
            abort(403, 'Unauthorized access.');
        }
        
        $loan = Loan::with(['employee.user', 'employee.team', 'payments', 'bankAccount'])->findOrFail($loanId);
        
        // Check team access for managers/team leaders
        if (!$user->isAdmin()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            if ($loan->employee->team_id && !$assignedTeamIds->contains($loan->employee->team_id)) {
                abort(403, 'Unauthorized access.');
            }
        }
        
        return view('loans.manage', compact('loan'));
    }

    /**
     * Update EMI amount for a loan
     */
    public function updateEmi(Request $request, $loanId)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isManager() && !$user->isTeamLeader()) {
            abort(403, 'Unauthorized access.');
        }
        
        $request->validate([
            'new_emi' => 'required|numeric|min:1',
            'reason' => 'required|string|max:500',
        ]);
        
        $loan = Loan::with(['employee.user', 'payments'])->findOrFail($loanId);
        
        // Check team access for managers/team leaders
        if (!$user->isAdmin()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            if ($loan->employee->team_id && !$assignedTeamIds->contains($loan->employee->team_id)) {
                abort(403, 'Unauthorized access.');
            }
        }
        
        $oldEmi = $loan->monthly_emi;
        $newEmi = $request->new_emi;
        
        // Calculate new tenure based on remaining balance
        $remainingBalance = $loan->remaining_balance;
        $newRemainingMonths = ceil($remainingBalance / $newEmi);
        
        DB::transaction(function () use ($loan, $newEmi, $newRemainingMonths, $oldEmi, $request) {
            $oldValues = [
                'monthly_emi' => $oldEmi,
                'remaining_months' => $loan->remaining_months,
            ];
            
            // Update loan
            $loan->monthly_emi = $newEmi;
            $loan->remaining_months = $newRemainingMonths;
            $loan->save();
            
            // Update pending payments
            $pendingPayments = $loan->payments()->where('status', 'pending')->orderBy('installment_number')->get();
            $remainingBalance = $loan->remaining_balance;
            
            foreach ($pendingPayments as $index => $payment) {
                if ($index === $pendingPayments->count() - 1) {
                    // Last payment gets the remaining balance
                    $payment->amount = $remainingBalance;
                } else {
                    $payment->amount = min($newEmi, $remainingBalance);
                    $remainingBalance -= $newEmi;
                }
                $payment->save();
            }
            
            // Log the change
            AuditLog::log(
                'emi_changed',
                'loans',
                "EMI changed for {$loan->employee->user->name}'s loan from ₹" . number_format($oldEmi, 2) . " to ₹" . number_format($newEmi, 2),
                $loan,
                $oldValues,
                ['monthly_emi' => $newEmi, 'remaining_months' => $newRemainingMonths],
                $request->reason
            );
        });
        
        event(new LoanStatusChanged($loan));
        
        return redirect()->back()->with('success', 'EMI updated successfully! New EMI: ₹' . number_format($newEmi, 2));
    }

    /**
     * Update tenure for a loan
     */
    public function updateTenure(Request $request, $loanId)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isManager() && !$user->isTeamLeader()) {
            abort(403, 'Unauthorized access.');
        }
        
        $request->validate([
            'new_tenure' => 'required|integer|min:1|max:360',
            'reason' => 'required|string|max:500',
        ]);
        
        $loan = Loan::with(['employee.user', 'payments'])->findOrFail($loanId);
        
        // Check team access for managers/team leaders
        if (!$user->isAdmin()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            if ($loan->employee->team_id && !$assignedTeamIds->contains($loan->employee->team_id)) {
                abort(403, 'Unauthorized access.');
            }
        }
        
        $oldTenure = $loan->remaining_months;
        $newTenure = $request->new_tenure;
        
        // Calculate new EMI based on remaining balance and new tenure
        $remainingBalance = $loan->remaining_balance;
        $newEmi = round($remainingBalance / $newTenure, 2);
        
        DB::transaction(function () use ($loan, $newEmi, $newTenure, $oldTenure, $request) {
            $oldValues = [
                'monthly_emi' => $loan->monthly_emi,
                'remaining_months' => $oldTenure,
                'total_months' => $loan->total_months,
            ];
            
            $oldEmi = $loan->monthly_emi;
            
            // Update loan
            $loan->monthly_emi = $newEmi;
            $loan->remaining_months = $newTenure;
            $loan->total_months = $loan->total_months - $oldTenure + $newTenure;
            $loan->save();
            
            // Delete existing pending payments and recreate
            $loan->payments()->where('status', 'pending')->delete();
            
            // Get the last paid payment to determine next installment number and due date
            $lastPaidPayment = $loan->payments()->where('status', 'paid')->orderBy('installment_number', 'desc')->first();
            $nextInstallmentNumber = $lastPaidPayment ? $lastPaidPayment->installment_number + 1 : 1;
            $nextDueDate = $lastPaidPayment ? $lastPaidPayment->due_date->copy()->addMonth() : $loan->start_date->copy();
            
            // Create new payment schedule
            $remainingBalance = $loan->remaining_balance;
            for ($i = 0; $i < $newTenure; $i++) {
                $amount = ($i === $newTenure - 1) ? $remainingBalance : min($newEmi, $remainingBalance);
                
                LoanPayment::create([
                    'loan_id' => $loan->id,
                    'installment_number' => $nextInstallmentNumber + $i,
                    'amount' => $amount,
                    'due_date' => $nextDueDate->copy()->addMonths($i),
                    'status' => 'pending',
                ]);
                
                $remainingBalance -= $newEmi;
            }
            
            // Log the change
            AuditLog::log(
                'tenure_changed',
                'loans',
                "Tenure changed for {$loan->employee->user->name}'s loan from {$oldTenure} months to {$newTenure} months. EMI changed from ₹" . number_format($oldEmi, 2) . " to ₹" . number_format($newEmi, 2),
                $loan,
                $oldValues,
                ['monthly_emi' => $newEmi, 'remaining_months' => $newTenure, 'total_months' => $loan->total_months],
                $request->reason
            );
        });
        
        event(new LoanStatusChanged($loan));
        
        return redirect()->back()->with('success', 'Tenure updated successfully! New tenure: ' . $newTenure . ' months, New EMI: ₹' . number_format($newEmi, 2));
    }

    /**
     * Pre-close a loan
     */
    public function preClose(Request $request, $loanId)
    {
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isManager() && !$user->isTeamLeader()) {
            abort(403, 'Unauthorized access.');
        }
        
        $request->validate([
            'settlement_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
            'settlement_date' => 'required|date',
        ]);
        
        $loan = Loan::with(['employee.user', 'payments'])->findOrFail($loanId);
        
        // Check team access for managers/team leaders
        if (!$user->isAdmin()) {
            $assignedTeamIds = $user->assignedTeams()->pluck('teams.id');
            if ($loan->employee->team_id && !$assignedTeamIds->contains($loan->employee->team_id)) {
                abort(403, 'Unauthorized access.');
            }
        }
        
        $settlementAmount = $request->settlement_amount;
        $originalBalance = $loan->remaining_balance;
        
        DB::transaction(function () use ($loan, $settlementAmount, $originalBalance, $request) {
            $oldValues = [
                'status' => $loan->status,
                'remaining_balance' => $originalBalance,
                'remaining_months' => $loan->remaining_months,
            ];
            
            // Mark all pending payments as cancelled
            $loan->payments()->where('status', 'pending')->update(['status' => 'cancelled']);
            
            // Create a settlement payment record
            LoanPayment::create([
                'loan_id' => $loan->id,
                'installment_number' => $loan->payments()->max('installment_number') + 1,
                'amount' => $settlementAmount,
                'due_date' => $request->settlement_date,
                'paid_date' => $request->settlement_date,
                'status' => 'paid',
            ]);
            
            // Update loan status
            $loan->status = 'closed';
            $loan->remaining_balance = 0;
            $loan->remaining_months = 0;
            $loan->save();
            
            // Log the change
            AuditLog::log(
                'pre_closure',
                'loans',
                "Loan pre-closed for {$loan->employee->user->name}. Original balance: ₹" . number_format($originalBalance, 2) . ", Settlement amount: ₹" . number_format($settlementAmount, 2),
                $loan,
                $oldValues,
                ['status' => 'closed', 'remaining_balance' => 0, 'remaining_months' => 0, 'settlement_amount' => $settlementAmount],
                $request->reason
            );
        });
        
        event(new LoanStatusChanged($loan));
        event(new DashboardUpdated('loan_closed', ['loan_id' => $loan->id]));
        
        $savings = $originalBalance - $settlementAmount;
        $message = 'Loan pre-closed successfully! Settlement amount: ₹' . number_format($settlementAmount, 2);
        if ($savings > 0) {
            $message .= ' (Savings: ₹' . number_format($savings, 2) . ')';
        }
        
        return redirect()->route('loans.employee', $loan->employee_id)->with('success', $message);
    }

    /**
     * Waive off remaining loan balance
     */
    public function waiveOff(Request $request, $loanId)
    {
        $user = auth()->user();
        
        // Only Admin can waive off loans
        if (!$user->isAdmin()) {
            abort(403, 'Only administrators can waive off loans.');
        }
        
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);
        
        $loan = Loan::with(['employee.user', 'payments'])->findOrFail($loanId);
        
        $originalBalance = $loan->remaining_balance;
        
        DB::transaction(function () use ($loan, $originalBalance, $request) {
            $oldValues = [
                'status' => $loan->status,
                'remaining_balance' => $originalBalance,
                'remaining_months' => $loan->remaining_months,
            ];
            
            // Mark all pending payments as waived
            $loan->payments()->where('status', 'pending')->update(['status' => 'waived']);
            
            // Update loan status
            $loan->status = 'waived';
            $loan->remaining_balance = 0;
            $loan->remaining_months = 0;
            $loan->save();
            
            // Log the change
            AuditLog::log(
                'waived',
                'loans',
                "Loan waived off for {$loan->employee->user->name}. Waived amount: ₹" . number_format($originalBalance, 2),
                $loan,
                $oldValues,
                ['status' => 'waived', 'remaining_balance' => 0, 'remaining_months' => 0],
                $request->reason
            );
        });
        
        event(new LoanStatusChanged($loan));
        event(new DashboardUpdated('loan_waived', ['loan_id' => $loan->id]));
        
        return redirect()->route('loans.employee', $loan->employee_id)->with('success', 'Loan waived off successfully! Waived amount: ₹' . number_format($originalBalance, 2));
    }
}
