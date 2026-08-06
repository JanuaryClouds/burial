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
        Schema::create('workflow_transitions', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('workflow_uuid')
                ->constrained('workflows', 'uuid')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid('from_stage_uuid')
                ->constrained('workflow_stages', 'uuid')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid('to_stage_uuid')
                ->constrained('workflow_stages', 'uuid')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId('permission_id')
                ->nullable()
                ->constrained('permissions')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_transitions');
    }
};
