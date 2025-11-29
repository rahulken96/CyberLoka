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
        Schema::create('development_applicants', function (Blueprint $table) {
            $table->id();
            $table->uuid('dev_app_code')->unique();
            $table->uuid('dev_code')->nullable();
            $table->uuid('user_code')->nullable();
            $table->enum('status', ["pending", "approved", "rejected"])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dev_code')->references('dev_code')->on('developments')->onDelete('cascade');
            $table->foreign('user_code')->references('user_code')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('development_applicants');
    }
};
