<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('cne')->nullable()->unique()->after('email');
            $table->date('birthday')->nullable()->after('cne');
            $table->string('filiere')->nullable()->after('birthday');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['cne']);
            $table->dropColumn(['cne', 'birthday', 'filiere']);
        });
    }
};
