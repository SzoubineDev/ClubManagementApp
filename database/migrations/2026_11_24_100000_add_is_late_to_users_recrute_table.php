<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Flag applications submitted after the regular recruitment season closed.
     *
     * Existing rows are back-filled with `false`, so only applications received
     * from this point onward (through the late recruitment endpoint) are marked
     * as late.
     */
    public function up(): void
    {
        Schema::table('users_recrute', function (Blueprint $table) {
            $table->boolean('is_late')
                ->default(false)
                ->after('status')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_recrute', function (Blueprint $table) {
            $table->dropIndex(['is_late']);
            $table->dropColumn('is_late');
        });
    }
};
