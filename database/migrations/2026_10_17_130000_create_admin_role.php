<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The UI "Promote to admin" action assigns a Spatie role named `admin`,
     * but that role never existed in the seeder — so promoted admins had NO
     * permissions at all (couldn't create events, publish, or manage members).
     * Create it here with the full management permission set.
     */
    public function up(): void
    {
        $now = now();

        // Idempotent role insert (avoids relying on Spatie's cache layer).
        $exists = DB::table('roles')->where('name', 'admin')->where('guard_name', 'web')->exists();

        if (! $exists) {
            $roleId = DB::table('roles')->insertGetId([
                'name' => 'admin',
                'guard_name' => 'web',
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        } else {
            $roleId = DB::table('roles')->where('name', 'admin')->where('guard_name', 'web')->value('id');
        }

        $permissions = [
            'profile.update',
            'members.viewAny',
            'members.view',
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
            'teams.viewAny',
            'teams.view',
            'teams.update',
            'teams.manageMembers',
            'documents.viewAny',
            'documents.create',
            'documents.update',
        ];

        $permRows = DB::table('permissions')
            ->whereIn('name', $permissions)
            ->where('guard_name', 'web')
            ->pluck('id')
            ->all();

        // Sync pivot rows directly (table from Spatie's schema).
        $existing = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->pluck('permission_id')
            ->all();

        $pivot = [];
        foreach ($permRows as $permId) {
            if (! in_array($permId, $existing)) {
                $pivot[] = ['permission_id' => $permId, 'role_id' => $roleId];
            }
        }

        if ($pivot !== []) {
            DB::table('role_has_permissions')->insert($pivot);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $roleId = DB::table('roles')->where('name', 'admin')->where('guard_name', 'web')->value('id');

        if ($roleId) {
            DB::table('role_has_permissions')->where('role_id', $roleId)->delete();
            DB::table('model_has_roles')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
