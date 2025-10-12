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
}
