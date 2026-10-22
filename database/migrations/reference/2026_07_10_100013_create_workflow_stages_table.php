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
            $table->string('name');
            $table->integer('position')->nullable();
            $table->text('description');
            $table->foreignId('permission_id')
                ->nullable()
                ->constrained('permissions')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['workflow_uuid', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['current_workflow_stage_uuid']);
            $table->dropColumn('current_workflow_stage_uuid');
        });

        Schema::dropIfExists('workflow_stages');
    }
};
