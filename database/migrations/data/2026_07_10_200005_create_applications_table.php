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
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('tracking_no')->unique();
            $table->string('qr_code')->unique();
            $table->foreignUuid('current_workflow_stage_uuid')
                ->nullable()
                ->constrained('workflow_stages', 'uuid')
                ->nullOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid('client_uuid')
                ->constrained('clients', 'uuid')
                ->cascadeOnDelete();
            $table->foreignUuid('beneficiary_uuid')
                ->constrained('beneficiaries', 'uuid')
                ->cascadeOnDelete();
            $table->foreignId('relationship_id')
                ->nullable()
                ->constrained('relationships')
                ->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('workflow_histories', function (Blueprint $table) {
            $table->foreignUuid('application_uuid')
                ->constrained('applications', 'uuid')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};
