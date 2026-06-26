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
        if (Schema::hasTable('family_members') && !Schema::hasColumn('family_members', 'role')) {
            Schema::table('family_members', function (Blueprint $table) {
                $table->enum('role', ['kepala', 'anggota'])->default('anggota')->after('updated_at');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('family_members') && Schema::hasColumn('family_members', 'role')) {
            Schema::table('family_members', function (Blueprint $table) {
                $table->dropColumn('role');
            });
        }
    }
};
