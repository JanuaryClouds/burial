<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->text('first_name')->change();
            $table->text('middle_name')->nullable()->change();
            $table->text('last_name')->change();
            $table->text('suffix')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('beneficiaries')->whereRaw('LENGTH(first_name) > 255 OR LENGTH(middle_name) > 255 OR LENGTH(last_name) > 255 OR LENGTH(suffix) > 255')->exists()) {
            throw new RuntimeException('Encrypted beneficiary names exceed the old column limit. Preserve them before rolling back.');
        }
        Schema::table('beneficiaries', function (Blueprint $table) {
            $table->string('first_name')->change();
            $table->string('middle_name')->nullable()->change();
            $table->string('last_name')->change();
            $table->string('suffix')->nullable()->change();
        });
    }
};
