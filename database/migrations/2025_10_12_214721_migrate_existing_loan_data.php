<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\Employee;
use App\Models\Loan;
use App\Models\LoanPayment;

return new class extends Migration
{
    public function up(): void
    {
        $employees = Employee::whereNotNull('loan')
            ->where('loan', '>', 0)
            ->get();

        foreach ($employees as $employee) {
            if ($employee->loan > 0 && $employee->emi > 0) {
                $months = ceil($employee->loan / $employee->emi);
                
                $bankAccountId = $employee->bankAccounts()->first()?->id;
                
                $loan = Loan::create([
                    'employee_id' => $employee->id,
                    'bank_account_id' => $bankAccountId,
                    'total_amount' => $employee->loan,
                    'monthly_emi' => $employee->emi,
                    'total_months' => $months,
                    'remaining_months' => $months,
                    'remaining_balance' => $employee->loan,
                    'start_date' => now(),
                    'status' => 'active',
                ]);

                for ($i = 1; $i <= $months; $i++) {
                    $amount = ($i === $months) ? ($employee->loan - ($employee->emi * ($months - 1))) : $employee->emi;
                    
                    LoanPayment::create([
                        'loan_id' => $loan->id,
                        'installment_number' => $i,
                        'amount' => $amount,
                        'due_date' => now()->addMonths($i - 1),
                        'status' => 'pending',
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        //
    }
};
