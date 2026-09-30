<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'profile.update',

            'members.viewAny',
            'members.view',
            'members.update',
            'members.delete',
            'members.assignRole',
            'members.assignTeam',

            'events.viewAny',
            'events.view',
            'events.create',
            'events.update',
            'events.delete',
            'events.publish',
            'events.rsvp',
            'events.manageAttendance',

            'announcements.viewAny',
            'announcements.create',
            'announcements.update',
            'announcements.delete',
            'announcements.publish',

            'posts.viewAny',
            'posts.create',
            'posts.update',
            'posts.delete',
            'posts.schedule',
            'posts.publish',

            'teams.viewAny',
            'teams.view',
            'teams.update',
            'teams.manageMembers',

            'documents.viewAny',
            'documents.create',
            'documents.update',
            'documents.delete',

            'settings.manage',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }

        // member — base role for everyone
        $member = Role::firstOrCreate(['name' => 'member']);
        $member->syncPermissions([
            'profile.update',
            'events.viewAny',
            'events.view',
            'events.rsvp',
            'announcements.viewAny',
            'posts.viewAny',
            'teams.viewAny',
            'teams.view',
            'documents.viewAny',
        ]);

        // team_lead — inherits member, scoped to their own team via policies
        $teamLead = Role::firstOrCreate(['name' => 'team_lead']);
        $teamLead->syncPermissions([
            ...$member->permissions->pluck('name')->toArray(),
            'members.viewAny',
            'members.view',
            'events.create',
            'events.update',
            'events.manageAttendance',
            'posts.create',
            'posts.update',
            'teams.update',
            'teams.manageMembers',
            'documents.create',
            'documents.update',
        ]);

        // social_media_manager
        $socialMedia = Role::firstOrCreate(['name' => 'social_media_manager']);
        $socialMedia->syncPermissions([
            ...$member->permissions->pluck('name')->toArray(),
            'posts.create',
            'posts.update',
            'posts.delete',
            'posts.schedule',
            'posts.publish',
        ]);

        // secretary_general
        $secretary = Role::firstOrCreate(['name' => 'secretary_general']);
        $secretary->syncPermissions([
            ...$member->permissions->pluck('name')->toArray(),
            'members.viewAny',
            'members.view',
            'events.create',
            'events.update',
            'events.delete',
            'events.publish',
            'events.manageAttendance',
            'announcements.create',
            'announcements.update',
            'announcements.delete',
            'announcements.publish',
            'documents.create',
            'documents.update',
            'documents.delete',
        ]);

        // rh (HR)
        $rh = Role::firstOrCreate(['name' => 'rh']);
        $rh->syncPermissions([
            ...$member->permissions->pluck('name')->toArray(),
            'members.viewAny',
            'members.view',
            'members.update',
            'members.delete',
            'members.assignRole',
            'members.assignTeam',
            'teams.manageMembers',
        ]);

        // vice_president — everything except settings + role assignment
        $vice = Role::firstOrCreate(['name' => 'vice_president']);
        $vice->syncPermissions(
            Permission::whereNotIn('name', ['settings.manage', 'members.assignRole'])
                ->pluck('name')
                ->toArray()
        );

        // president — everything
        $president = Role::firstOrCreate(['name' => 'president']);
        $president->syncPermissions(Permission::all());
    }
}
