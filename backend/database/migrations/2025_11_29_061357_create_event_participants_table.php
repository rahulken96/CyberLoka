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
        Schema::create('event_participants', function (Blueprint $table) {
            $table->id();
            $table->uuid('event_participant_code')->unique();
            $table->uuid('event_code')->nullable();
            $table->uuid('family_code')->nullable();
            $table->integer('qty')->nullable();
            $table->decimal('total_price', 10)->nullable();
            $table->integer('payment_status')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('event_code')->references('event_code')->on('events')->onDelete('cascade');
            $table->foreign('family_code')->references('family_code')->on('head_of_families')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event_participants');
    }
};
