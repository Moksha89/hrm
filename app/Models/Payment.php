<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payment_type',
        'employee_id',
        'amount',
        'loan_id',
        'loan_payment_id',
        'bank_account_id',
        'status',
        'approval_status',
        'processed_by',
        'transaction_date',
        'notes',
        'utr',
        'remarks',
        'screenshot',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'transaction_date' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function loanPayment()
    {
        return $this->belongsTo(LoanPayment::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
