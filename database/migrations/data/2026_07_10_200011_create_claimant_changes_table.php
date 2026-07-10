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
        Schema::create('client_changes', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('application_uuid')->constrained('applications', 'uuid')->onDelete('CASCADE');
            $table->foreignUuid('old_client_uuid')->constrained('clients', 'uuid')->onDelete('CASCADE');
            $table->foreignUuid('new_client_uuid')->constrained('clients', 'uuid')->onDelete('CASCADE');
            $table->string('reason');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claimant_changes');
    }
};
