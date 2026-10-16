<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Mirror the manual "ALTER TABLE events ADD COLUMN status ENUM(...)"
     * change in a migration so every environment (and CI) stays in sync.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('events', 'status')) {
            Schema::table('events', function (Blueprint $table) {
                $table->enum('status', ['published', 'draft'])
                    ->default('draft')
                    ->after('team');
            });
        }

        // Backfill: any event that already has a past published_at is official.
        DB::table('events')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->update(['status' => 'published']);

        // Keep the two representations consistent going forward.
        DB::table('events')
            ->where('status', 'published')
            ->whereNull('published_at')
            ->update(['published_at' => now()]);
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
