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
            $table->date('application_date');
            $table->string('swa')->nullable();
            $table->foreignUuid('encoder')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->onUpdate('cascade');
            $table->string('funeraria');
            $table->string('amount')->nullable();
            $table->enum('status', [
                'pending',
                'processing',
                'approved',
                'released'])
                ->default('pending');
            $table->string('remarks')->nullable();
            $table->foreignUuid('initial_checker')
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
