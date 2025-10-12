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
            TeamSeeder::class,
        ]);

        $adminRole = Role::where('slug', 'admin')->first();

        $admin = User::create([
            'name' => env('ADMIN_NAME', 'admin'),
            'mobile' => env('ADMIN_MOBILE', '0000000000'),
            'password' => bcrypt(env('ADMIN_PASSWORD', 'password')),
            'password_changed_at' => now(),
        ]);

        $admin->roles()->attach($adminRole);
    }
}
