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
        Schema::create('addresses', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->uuidMorphs('addressable');
            $table->string('region_code');
            $table->string('region_name');
            $table->string('province_code')->nullable();
            $table->string('province_name')->nullable();
            $table->string('municipality_code')->nullable();
            $table->string('municipality_name')->nullable();
            $table->string('barangay_code');
            $table->string('barangay_name');
            $table->text('street');
            $table->text('house_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
