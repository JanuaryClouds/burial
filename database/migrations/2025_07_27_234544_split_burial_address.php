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
        Schema::table('burial_services', function (Blueprint $table) {
            Schema::dropColumns('burial_services', ['burial_address']);
            $table->string('burial_address')->nullable()->after('rep_relationship');
            $table->foreignId('barangay_id')
                ->nullable()
                ->constrained('barangays')
                ->nullOnDelete()
                ->after('burial_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('burial_services', function (Blueprint $table) {
            //
        });
    }
};
