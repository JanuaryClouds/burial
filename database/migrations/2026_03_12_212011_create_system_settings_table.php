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
        Schema::create('system_settings', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->boolean('maintenance_mode')->default(false);
            $table->string('social_welfare_officer')->nullable();
            $table->string('dept_head')->nullable();
            $table->string('department_email')->nullable();
            // Extend more system settings here

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
