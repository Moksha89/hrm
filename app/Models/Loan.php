<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'bank_account_id',
        'total_amount',
        'monthly_emi',
        'total_months',
        'remaining_months',
        'remaining_balance',
        'start_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'monthly_emi' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
            'start_date' => 'date',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function bankAccount()
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function payments()
    {
        return $this->hasMany(LoanPayment::class);
    }
}
