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
            $table->uuid('id')->primary();

            $table->foreignUuid('burial_assistance_id')
                ->constrained('burial_assistances', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignUuid('claimant_id')
                ->constrained('claimants', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->nullableMorphs('loggable');
            $table->boolean('is_progress_step')->default(true);
            $table->date('date_in');
            $table->date('date_out')->nullable();
            $table->string('comments')->nullable();
            $table->json('extra_data')->nullable();
            $table->foreignId('added_by')
                ->nullable()
                ->constrained('users', 'id')
                ->onUpdate('cascade')
                ->onDelete('cascade');

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
