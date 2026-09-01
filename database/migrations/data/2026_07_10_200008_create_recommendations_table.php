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
        Schema::create('recommendations', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('application_uuid')
                ->constrained('applications', 'uuid')
                ->cascadeOnDelete();
            $table->foreignUuid('funeral_assistance_type_uuid')
                ->constrained('funeral_assistance_types', 'uuid')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('amount_extended', 10, 2)->nullable();
            $table->foreignId('mode_of_assistance_id')
                ->nullable()
                ->constrained('mode_of_assistances')
                ->nullOnDelete();
            $table->foreignId('recommended_by')
                ->constrained('users', 'id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->enum('status', ['pending', 'approved', 'cancelled', 'rejected'])->default('pending');
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::table('workflow_histories', function (Blueprint $table) {
            $table->foreignUuid('recommendation_uuid')
                ->after('uuid')
                ->constrained('recommendations', 'uuid')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendations');
    }
};
