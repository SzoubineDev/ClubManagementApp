<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Roles & permissions first — the test user needs the member role to exist
        $this->call([
            RolePermissionSeeder::class,
        ]);

        $president = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Give the test user every role so you can test everything while building
        $president->assignRole([
            'president',
            'vice_president',
            'rh',
            'secretary_general',
            'social_media_manager',
            'team_lead',
        ]);

        // Example scoped team lead for testing policies
        $arabicLead = User::factory()->create([
            'name' => 'Arabic Lead',
            'email' => 'arabic.lead@example.com',
            'team' => 'arabic',
        ]);
        $arabicLead->assignRole('team_lead');
    }
}
