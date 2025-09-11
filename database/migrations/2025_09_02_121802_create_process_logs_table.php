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
        Schema::create('process_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('burial_assistance_id')
                ->constrained('burial_assistances')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('workflow_step_id')
                ->constrained('workflow_steps')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->date('date_in');
            $table->date('date_out')->nullable();
            $table->string('comments')->nullable();
            $table->json('extra_data')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('process_logs');
    }
};
