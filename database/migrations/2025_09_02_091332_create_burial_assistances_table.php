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
            $table->uuid('id')->primary();
            $table->string('tracking_no')->unique();
            $table->string('tracking_code')->unique();
            $table->date('application_date');
            $table->string('swa')->nullable();
            $table->foreignId('encoder')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->string('funeraria');
            $table->foreignUuid('deceased_id')
                ->constrained('deceased', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignUuid('claimant_id')
                ->constrained('claimants', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('amount')->nullable();
            $table->enum('status', [
                'pending', 
                'processing',
                'approved', 
                'released', 
                'rejected'])
                ->default('pending');
            $table->string('remarks')->nullable();
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
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
