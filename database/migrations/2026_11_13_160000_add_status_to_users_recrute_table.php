<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add the review status to recruitment applications.
     *
     * Uses the same enum convention as `preferred_section` on this table.
     * The default of `pending` back-fills every existing row automatically
     * (MySQL applies the default to current records when a defaulted column
     * is added), so no data is lost and no manual back-fill is required.
     */
    public function up(): void
    {
        Schema::table('users_recrute', function (Blueprint $table) {
            $table->enum('status', ['pending', 'accepted', 'rejected', 'passed_to_test'])
                ->default('pending')
                ->after('preferred_section')
                ->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users_recrute', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropColumn('status');
        });
    }
};
