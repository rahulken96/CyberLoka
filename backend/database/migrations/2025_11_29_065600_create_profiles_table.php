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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->uuid('profile_code')->unique();
            $table->uuid('user_code')->nullable();
            $table->string('image')->nullable();
            $table->string('name')->nullable();
            $table->longText('about')->nullable();
            $table->string('headman')->nullable();
            $table->integer('people')->nullable();
            $table->decimal('agricultural_area', 10)->nullable();
            $table->decimal('total_area', 10)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_code')->references('user_code')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
