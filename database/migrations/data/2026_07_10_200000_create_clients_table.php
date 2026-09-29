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
        Schema::create('clients', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('date_of_birth');
            $table->string('region_code');
            $table->string('province_code')->nullable();
            $table->string('municipality_code')->nullable();
            $table->string('barangay_code');
            $table->text('street');
            $table->text('house_no');
            $table->string('contact_number');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
