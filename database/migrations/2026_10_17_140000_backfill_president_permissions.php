<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The president must hold EVERY permission (see RolePermissionSeeder).
     * If the seeder was not re-run after new permissions were added, the
     * pivot rows are missing and the president cannot create/publish
     * events. Backfill them directly here (idempotent), then flush cache.
     */
    public function up(): void
    {
        $roleId = DB::table('roles')->where('name', 'president')->where('guard_name', 'web')->value('id');

        if (! $roleId) {
            return;
        }

        $existing = DB::table('role_has_permissions')
            ->where('role_id', $roleId)
            ->pluck('permission_id')
            ->all();

        $missing = DB::table('permissions')
            ->where('guard_name', 'web')
            ->pluck('id')
            ->diff($existing)
            ->all();

        $pivot = array_map(fn ($pid) => ['permission_id' => $pid, 'role_id' => $roleId], $missing);

        if ($pivot !== []) {
            DB::table('role_has_permissions')->insert($pivot);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Non-destructive: we only ADDED missing permissions.
    }
};
