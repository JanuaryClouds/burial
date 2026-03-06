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
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tracking_no')->unique();
            $table->integer('age');
            $table->date('date_of_birth');
            $table->string('house_no');
            $table->string('street');
            $table->foreignId('district_id')->constrained('districts');
            $table->foreignId('barangay_id')->constrained('barangays');
            $table->string('city')->default('Taguig City');
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
