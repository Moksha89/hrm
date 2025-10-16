<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function teamAssignments()
    {
        return $this->hasMany(TeamAssignment::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'team_assignments')->withPivot('role_type')->withTimestamps();
    }
}
