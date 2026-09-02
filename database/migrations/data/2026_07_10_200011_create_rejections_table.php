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
        Schema::create('rejections', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('application_uuid')
                ->constrained('applications', 'uuid')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->text('reason');
            $table->foreignId('rejected_by')
                ->constrained('users', 'id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rejections');
    }
};
