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
        Schema::create('family_members', function (Blueprint $table) {
            $table->id();
            $table->uuid('family_member_code')->unique();
            $table->uuid('family_code');
            $table->uuid('user_code');
            $table->date('date_of_birth')->nullable();
            $table->string('image')->nullable();
            $table->string('occupation')->nullable();
            $table->integer('nik')->nullable();
            $table->enum('gender', ["pria", "wanita"])->nullable();
            $table->enum('martial_status', ["single", "menikah"])->nullable();
            $table->enum('relation', ["kakek", "nenek", "ibu", "ayah", "anak", "saudara"])->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('family_code')->references('family_code')->on('head_of_families')->onDelete('cascade');
            $table->foreign('user_code')->references('user_code')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_members');
    }
};
