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
    }
}
