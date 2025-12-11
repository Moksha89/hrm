<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeWorkingDay extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'month',
        'year',
        'working_days',
        'deduct_emi',
        'emi_override_amount',
        'emi_override_reason',
        'emi_override_by',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'integer',
            'year' => 'integer',
            'working_days' => 'integer',
            'deduct_emi' => 'boolean',
            'emi_override_amount' => 'decimal:2',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function overrideBy()
    {
        return $this->belongsTo(User::class, 'emi_override_by');
    }
}
