<?php

namespace Database\Seeders;

use App\Models\Team;
use Illuminate\Database\Seeder;

class TeamSeeder extends Seeder
{
    public function run(): void
    {
        $teams = [
            'Development',
            'Design',
            'Marketing',
            'Sales',
            'HR',
            'Finance',
            'Operations',
        ];

        foreach ($teams as $team) {
            Team::create(['name' => $team]);
        }
    }
}
