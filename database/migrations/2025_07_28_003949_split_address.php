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
        // Schema::table('burial_service_providers', function (Blueprint $table) {
        //     Schema::dropColumns('burial_service_providers', ['address']);
        // });

        // Schema::table('burial_service_providers', function (Blueprint $table) {
        //     $table->string('address')->after('contact_details');
        //     $table->foreignId('barangay_id')
        //         ->nullable()
        //         ->constrained('barangays')
        //         ->nullOnDelete()
        //         ->after('address');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('burial_service_providers', function (Blueprint $table) {
            //
        });
    }
};
