<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repairs the events.status column for environments where it was added
     * manually via phpMyAdmin with the values in the wrong ENUM order:
     *
     *   ALTER TABLE events ADD COLUMN status ENUM('published','draft') ...
     *
     * Laravel's validation rule `in:draft,published` compares against MySQL's
     * strict-quoted enum list, and a mismatched order caused "status must be
     * draft or published" failures on every publish attempt. We rebuild the
     * column as enum('draft','published') NOT NULL DEFAULT 'draft' and
     * backfill existing rows so nothing disappears from public views.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('events', 'status')) {
            return; // handled by 2026_10_17_100000_add_status_to_events_table
        }

        // MySQL-only repair: normalize the ENUM order + default (idempotent).
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE events MODIFY status ENUM('draft','published') NOT NULL DEFAULT 'draft'");
        }

        // Backfill legacy rows that predate the status column.
        DB::table('events')->whereNull('status')->update(['status' => 'published']);

        DB::table('events')
            ->where('status', 'published')
            ->whereNull('published_at')
            ->whereNotNull('created_at')
            ->update(['published_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        // No destructive rollback: keep the corrected column.
    }
};
