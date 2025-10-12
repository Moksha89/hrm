<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanPayment;
use App\Models\Payment;
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
}
