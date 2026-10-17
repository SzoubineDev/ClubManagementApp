<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The events table was created without a `filiere` column, but the model,
     * forms and validation all reference it. On MySQL strict mode this makes
     * every "Save event" / "Draft event" insert fail with:
     *   SQLSTATE[42S22]: Column not found: 1054 Unknown column 'filiere'
     */
    public function up(): void
    {
        if (! Schema::hasColumn('events', 'filiere')) {
            Schema::table('events', function (Blueprint $table) {
                $table->string('filiere')->nullable()->after('team');
            });
        }
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('filiere');
        });
    }
};
