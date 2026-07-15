<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('events', 'active_date_event_date')) {
            DB::statement("ALTER TABLE events ADD COLUMN active_date_event_date DATE GENERATED ALWAYS AS (IF(deleted_at IS NULL, DATE(date_event), NULL)) STORED");
        }

        Schema::table('events', function (Blueprint $table) {
            $table->unique(['name', 'active_date_event_date'], 'events_active_name_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropUnique('events_active_name_date_unique');
            $table->dropColumn('active_date_event_date');
        });
    }
};
