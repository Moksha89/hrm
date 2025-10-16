<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeActivity extends Model
{
    protected $fillable = [
        'employee_id',
        'activity_type',
        'description',
        'data',
        'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function performedBy()
    {
        return $this->belongsTo(User::class, 'performed_by');
    }
}
