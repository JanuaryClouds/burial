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
        Schema::create('workflow_histories', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('from_stage_uuid')
                ->nullable()
                ->constrained('workflow_stages', 'uuid')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid('to_stage_uuid')
                ->nullable()
                ->constrained('workflow_stages', 'uuid')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('date_in');
            $table->dateTime('date_out');
            $table->text('reason');
            $table->foreignId('processed_by')
                ->nullable()
                ->constrained('users', 'id')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_histories');
    }
};
