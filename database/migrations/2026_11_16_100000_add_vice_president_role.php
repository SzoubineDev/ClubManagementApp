<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * The vice-president role is referenced across the policies
     * (EventPolicy, MemberPolicy, ApplicationPolicy, VideoPolicy) and by
     * User::isManager(), but many databases only ever got the
     * member / president / admin roles. Insert it idempotently so this
     * migration is safe to re-run and safe on a database that already
     * has it via RolePermissionSeeder.
     */
    public function up(): void
    {
        $now = now();

        DB::table('roles')->updateOrInsert(
            ['name' => 'vice_president', 'guard_name' => 'web'],
            ['created_at' => $now, 'updated_at' => $now]
        );

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        $roleId = DB::table('roles')
            ->where('name', 'vice_president')
            ->where('guard_name', 'web')
            ->value('id');

        if ($roleId !== null) {
            DB::table('role_has_permissions')->where('role_id', $roleId)->delete();
            DB::table('model_has_roles')->where('role_id', $roleId)->delete();
            DB::table('roles')->where('id', $roleId)->delete();
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
