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
        Schema::create('profile_images', function (Blueprint $table) {
            $table->id();
            $table->uuid('profile_image_code')->unique();
            $table->uuid('profile_code')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('profile_code')->references('profile_code')->on('profiles')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_images');
    }
};
