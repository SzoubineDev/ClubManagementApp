<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The media manager and the design manager each own a separate video
     * library. `library` keeps the two pools apart so neither role can see
     * or edit the other's videos.
     */
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('library')->default('media')->after('sort_order')->index();
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex(['library']);
            $table->dropColumn('library');
        });
    }
};
