<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'gross_salary',
        'working_days',
        'net_pay',
        'total_emi',
        'final_pay',
        'bank_account_id',
        'status',
        'approval_status',
        'processed_by',
        'payment_date',
        'notes',
        'utr',
        'remarks',
        'screenshot',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'gross_salary' => 'decimal:2',
            'working_days' => 'integer',
            'net_pay' => 'decimal:2',
            'total_emi' => 'decimal:2',
            'final_pay' => 'decimal:2',
            'payment_date' => 'date',
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

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
