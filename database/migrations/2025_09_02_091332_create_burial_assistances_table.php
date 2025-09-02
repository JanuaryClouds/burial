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
        Schema::create('burial_assistances', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_no')->unique();
            $table->date('application_date');
            $table->string('swa');
            $table->foreignId('encoder')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->string('funeraria');
            $table->foreignId('deceased_id')
                ->constrained('deceased')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('claimant_id')
                ->constrained('claimants')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('amount');
            $table->enum('status', [
                'pending', 
                'processing',
                'approved', 
                'released', 
                'rejected'])
                ->default('pending');
            $table->string('remarks')->nullable();
            $table->foreignId('initial_checker')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('burial_assistances');
    }
};
