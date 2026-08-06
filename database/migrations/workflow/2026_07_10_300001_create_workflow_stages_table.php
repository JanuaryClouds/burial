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
        Schema::create('workflow_stages', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('workflow_uuid')
                ->constrained('workflows', 'uuid')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->text('description');
            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignUuid('current_workflow_stage_uuid')
                ->after('qr_code')
                ->nullable()
                ->constrained('workflow_stages', 'uuid')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_stages');
    }
};
