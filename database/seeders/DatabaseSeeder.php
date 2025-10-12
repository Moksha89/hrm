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

        $admin = User::create([
            'name' => env('ADMIN_NAME', 'admin'),
            'mobile' => env('ADMIN_MOBILE', '0000000000'),
            'password' => bcrypt(env('ADMIN_PASSWORD', 'password')),
        ]);

        $admin->roles()->attach($adminRole);
    }
}
