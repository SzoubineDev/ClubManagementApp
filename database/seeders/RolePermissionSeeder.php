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
            'documents.download',

            'attendance.viewAny',
            'attendance.manageSection',

            'videos.viewAny',
            'videos.create',
            'videos.update',
            'videos.delete',

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

        // section_chef — leads one debate section (Arabic / English / French)
        $sectionChef = Role::firstOrCreate(['name' => 'section_chef']);
        $sectionChef->syncPermissions([
            ...$member->permissions->pluck('name')->toArray(),
            'members.viewAny',
            'members.view',
            'attendance.manageSection',
        ]);

        // rh_manager — read-only member directory + daily presence history
        $rhManager = Role::firstOrCreate(['name' => 'rh_manager']);
        $rhManager->syncPermissions([
            ...$member->permissions->pluck('name')->toArray(),
            'members.viewAny',
            'members.view',
            'attendance.viewAny',
        ]);

        // media_manager — owns the YouTube media library
        $mediaManager = Role::firstOrCreate(['name' => 'media_manager']);
        $mediaManager->syncPermissions([
            ...$member->permissions->pluck('name')->toArray(),
            'videos.viewAny',
            'videos.create',
            'videos.update',
            'videos.delete',
        ]);

        // design_manager — creative/branding role (no extra permissions yet)
        $designManager = Role::firstOrCreate(['name' => 'design_manager']);
        $designManager->syncPermissions([
            ...$member->permissions->pluck('name')->toArray(),
        ]);

        // assistant_general — official document downloads
        $assistantGeneral = Role::firstOrCreate(['name' => 'assistant_general']);
        $assistantGeneral->syncPermissions([
            ...$member->permissions->pluck('name')->toArray(),
            'documents.viewAny',
            'documents.download',
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

        // admin — full management (created via migration for existing DBs;
        // added here so fresh seeds get it too)
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
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
            'posts.create',
            'posts.update',
            'posts.delete',
            'teams.update',
            'teams.manageMembers',
            'documents.create',
            'documents.update',
            'documents.download',
            'attendance.viewAny',
            'attendance.manageSection',
            'videos.viewAny',
            'videos.create',
            'videos.update',
            'videos.delete',
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
