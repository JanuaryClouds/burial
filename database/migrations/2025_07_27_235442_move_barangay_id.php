<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('burial_services', 'barangay_id')) {
            Schema::table('burial_services', function (Blueprint $table) {
                $table->dropForeign(['barangay_id']);
                $table->dropColumn('barangay_id');
            });
        }

        Schema::table('burial_services', function (Blueprint $table) {
            $table->foreignId('barangay_id')
                ->nullable()
                ->constrained('barangays')
                ->nullOnDelete()
                ->after('rep_relationship');
        });
        DB::statement('ALTER TABLE burial_services MODIFY barangay_id BIGINT UNSIGNED NULL AFTER burial_address');

    }

    public function down(): void
    {
        Schema::table('burial_services', function (Blueprint $table) {
            $table->dropForeign(['barangay_id']);
            $table->dropColumn('barangay_id');
        });
    }
};
