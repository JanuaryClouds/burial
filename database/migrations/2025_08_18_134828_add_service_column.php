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
        Schema::table('burial_assistance_requests', function (Blueprint $table) {
            $table->foreignId('service_id')
                ->nullable()
                ->after('type_of_assistance')
                ->constrained('burial_services')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('burial_assistance_requests', function (Blueprint $table) {
            Schema::dropColumns('burial_assistance_requests', ['service_id']);
        });
    }
};
