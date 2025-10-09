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
        Schema::create('cheques', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('burial_assistance_id')
                ->constrained('burial_assistances', 'id')
                ->onDelete('cascade');
            $table->foreignUuid('claimant_id')
                ->constrained('claimants', 'id')
                ->onDelete('cascade');
            $table->string('obr_number')->unique();
            $table->string('cheque_number')->unique()->nullable();
            $table->string('dv_number')->unique()->nullable();
            $table->decimal('amount')->nullable();
            $table->date('date_issued')->nullable();
            $table->date('date_claimed')->nullable();
            $table->enum('status', ['issued', 'claimed', 'cancelled'])->default('issued');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};
