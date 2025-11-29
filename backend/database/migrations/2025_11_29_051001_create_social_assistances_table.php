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
        Schema::create('social_assistances', function (Blueprint $table) {
            $table->id();
            $table->uuid('social_code')->unique();
            $table->string('image')->nullable();
            $table->string('name')->nullable();
            $table->enum('category', ["sembako", "uang", "bbm", "medis"])->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->string('provider')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_assistances');
    }
};
