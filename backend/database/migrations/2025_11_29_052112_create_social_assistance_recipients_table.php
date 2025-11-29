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
        Schema::create('social_assistance_recipients', function (Blueprint $table) {
            $table->id();
            $table->uuid('social_recipient_code')->unique();
            $table->uuid('social_code')->nullable();
            $table->uuid('family_code')->nullable();
            $table->string('bank')->nullable();
            $table->string('account_bank')->nullable();
            $table->decimal('amount', 10)->nullable();
            $table->string('image')->nullable()->comment('Bukti (Proof) penerimaan bansos');
            $table->longText('reason')->nullable();
            $table->enum('status', ["pending", "approved", "rejected"])->default('pending');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('social_code')->references('social_code')->on('social_assistances')->onDelete('cascade');
            $table->foreign('family_code')->references('family_code')->on('head_of_families')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('social_assistance_recipients');
    }
};
