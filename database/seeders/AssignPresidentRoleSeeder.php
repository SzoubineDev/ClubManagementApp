<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignPresidentRoleSeeder extends Seeder
{
    public function run(): void
    {
        $presidentRole = Role::firstOrCreate([
            'name' => 'president',
            'guard_name' => 'web',
        ]);

        $user = User::firstOrCreate(
            ['email' => 'president@example.com'],
            [
                'name' => 'President',
                'password' => bcrypt('ChangeMe123!'),
            ]
        );

        $user->assignRole($presidentRole);

        $this->command->info('President account created/verified and role assigned.');
    }
}

