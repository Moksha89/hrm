<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'team_id',
        'salary',
        'loan',
        'emi',
        'aadhar',
        'pan',
        'dob',
        'date_of_joining',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'salary' => 'decimal:2',
            'loan' => 'decimal:2',
            'emi' => 'decimal:2',
            'dob' => 'date',
            'date_of_joining' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(BankAccount::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function workingDays()
    {
        return $this->hasMany(EmployeeWorkingDay::class);
    }

    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    public function activeLoans()
    {
        return $this->loans()->where('status', 'active')->where('remaining_balance', '>', 0);
    }

    public function getTotalMonthlyEmi()
    {
        return $this->activeLoans()->sum('monthly_emi');
    }

    public function getNetPay($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        $workingDay = $this->workingDays()
            ->where('month', $month)
            ->where('year', $year)
            ->first();
        
        $days = $workingDay ? $workingDay->working_days : 30;
        $dailySalary = ($this->salary ?? 0) / 30;
        
        return round($dailySalary * $days, 2);
    }

    public function getFinalPay($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        $netPay = $this->getNetPay($month, $year);
        
        $workingDay = $this->workingDays()
            ->where('month', $month)
            ->where('year', $year)
            ->first();
        
        $shouldDeductEmi = $workingDay ? ($workingDay->deduct_emi ?? true) : true;
        $monthlyEmi = $shouldDeductEmi ? $this->getTotalMonthlyEmi() : 0;
        
        return max(0, round($netPay - $monthlyEmi, 2));
    }

    public function getWorkingDays($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;
        
        $workingDay = $this->workingDays()
            ->where('month', $month)
            ->where('year', $year)
            ->first();
        
        return $workingDay ? $workingDay->working_days : 0;
    }

    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }
}
