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
        Schema::create('client_recommendations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('CASCADE');
            $table->foreignId('assistance_id')->constrained('assistances');
            $table->string('referral')->nullable();
            $table->string('amount')->nullable();
            $table->foreignId('moa_id')->constrained('mode_of_assistances')->nullable();
            $table->string('others')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_recommendations');
    }
};