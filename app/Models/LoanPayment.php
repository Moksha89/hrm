<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_id',
        'installment_number',
        'amount',
        'due_date',
        'status',
        'paid_date',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'due_date' => 'date',
            'paid_date' => 'date',
        ];
    }

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}
