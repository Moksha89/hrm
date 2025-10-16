<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin'],
            ['name' => 'Manager', 'slug' => 'manager'],
            ['name' => 'Team Leader', 'slug' => 'team-leader'],
            ['name' => 'Employee', 'slug' => 'employee'],
            ['name' => 'HR', 'slug' => 'hr'],
            ['name' => 'Accountant', 'slug' => 'accountant'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                ['name' => $role['name']]
            );
        }
    }
}
