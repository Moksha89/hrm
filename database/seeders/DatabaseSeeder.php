<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $teamLeaderRole = Role::where('slug', 'team-leader')->first();
        $hrRole = Role::where('slug', 'hr')->first();
        $accountantRole = Role::where('slug', 'accountant')->first();
        $employeeRole = Role::where('slug', 'employee')->first();
        
        $adminUser = User::updateOrCreate(
            ['mobile' => '9182982174'],
            [
                'name' => 'jvv',
                'email' => 'jvv@example.com',
                'password' => bcrypt('Sarkar@00'),
                'password_changed_at' => now(),
            ]
        );
        $adminUser->roles()->sync([$adminRole->id]);
        
        $manager = User::updateOrCreate(
            ['mobile' => '9876543210'],
            [
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'password' => bcrypt('Password@00'),
                'password_changed_at' => now(),
            ]
        );
        $manager->roles()->sync([$managerRole->id]);
        
        $teamLeader = User::updateOrCreate(
            ['mobile' => '9876543211'],
            [
                'name' => 'Team Leader User',
                'email' => 'teamleader@example.com',
                'password' => bcrypt('Password@00'),
                'password_changed_at' => now(),
            ]
        );
        $teamLeader->roles()->sync([$teamLeaderRole->id]);
        
        $hr = User::updateOrCreate(
            ['mobile' => '9876543212'],
            [
                'name' => 'HR User',
                'email' => 'hr@example.com',
                'password' => bcrypt('Password@00'),
                'password_changed_at' => now(),
            ]
        );
        $hr->roles()->sync([$hrRole->id]);
        
        $accountant = User::updateOrCreate(
            ['mobile' => '9876543213'],
            [
                'name' => 'Accountant User',
                'email' => 'accountant@example.com',
                'password' => bcrypt('Password@00'),
                'password_changed_at' => now(),
            ]
        );
        $accountant->roles()->sync([$accountantRole->id]);
        
        $employee = User::updateOrCreate(
            ['mobile' => '9999999999'],
            [
                'name' => 'Employee User',
                'email' => 'employee@example.com',
                'password' => bcrypt('Password@00'),
                'password_changed_at' => now(),
            ]
        );
        $employee->roles()->sync([$employeeRole->id]);
    }
}
