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
        Schema::create('closures', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('application_uuid')
                ->constrained('applications', 'uuid')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->dateTime('closed_at');
            $table->foreignId('closed_by')
                ->constrained('users', 'id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->text('reason');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('closures');
    }
};
