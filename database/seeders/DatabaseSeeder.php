<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Team;
use App\Models\TeamAssignment;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            TeamSeeder::class,
        ]);

        $adminRole = Role::where('slug', 'admin')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $teamLeaderRole = Role::where('slug', 'team-leader')->first();
        $hrRole = Role::where('slug', 'hr')->first();
        $accountantRole = Role::where('slug', 'accountant')->first();
        $employeeRole = Role::where('slug', 'employee')->first();
        
        $teams = Team::all();
        
        $adminUser = User::updateOrCreate(
            ['mobile' => env('ADMIN_MOBILE', '9182982174')],
            [
                'name' => env('ADMIN_NAME', 'jvv'),
                'email' => 'jvv@example.com',
                'password' => bcrypt(env('ADMIN_PASSWORD', 'password')),
                'password_changed_at' => now(),
            ]
        );
        $adminUser->roles()->sync([$adminRole->id]);
        
        $manager = User::updateOrCreate(
            ['mobile' => env('MANAGER_MOBILE', '9876543210')],
            [
                'name' => 'Manager User',
                'email' => 'manager@example.com',
                'password' => bcrypt(env('MANAGER_PASSWORD', 'password')),
                'password_changed_at' => now(),
            ]
        );
        $manager->roles()->sync([$managerRole->id]);
        foreach ($teams->take(3) as $team) {
            TeamAssignment::updateOrCreate([
                'user_id' => $manager->id,
                'team_id' => $team->id,
            ], [
                'role_type' => 'manager',
            ]);
        }
        
        $teamLeader = User::updateOrCreate(
            ['mobile' => env('TEAM_LEADER_MOBILE', '9876543211')],
            [
                'name' => 'Team Leader User',
                'email' => 'teamleader@example.com',
                'password' => bcrypt(env('TEAM_LEADER_PASSWORD', 'password')),
                'password_changed_at' => now(),
            ]
        );
        $teamLeader->roles()->sync([$teamLeaderRole->id]);
        TeamAssignment::updateOrCreate([
            'user_id' => $teamLeader->id,
            'team_id' => $teams->first()->id,
        ], [
            'role_type' => 'team-leader',
        ]);
        
        $hr = User::updateOrCreate(
            ['mobile' => env('HR_MOBILE', '9876543212')],
            [
                'name' => 'HR User',
                'email' => 'hr@example.com',
                'password' => bcrypt(env('HR_PASSWORD', 'password')),
                'password_changed_at' => now(),
            ]
        );
        $hr->roles()->sync([$hrRole->id]);
        
        $accountant = User::updateOrCreate(
            ['mobile' => env('ACCOUNTANT_MOBILE', '9876543213')],
            [
                'name' => 'Accountant User',
                'email' => 'accountant@example.com',
                'password' => bcrypt(env('ACCOUNTANT_PASSWORD', 'password')),
                'password_changed_at' => now(),
            ]
        );
        $accountant->roles()->sync([$accountantRole->id]);
    }
}
