<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_beneficiaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->onDelete('CASCADE');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->foreignId('sex_id')->constrained('sexes');
            $table->foreignId('religion_id')->constrained('religions')->onDelete('cascade')->onUpdate('cascade');
            $table->date('date_of_birth');
            $table->date('date_of_death')->nullable();
            $table->string('place_of_birth');
            $table->foreignId('barangay_id')->constrained('barangays')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('client_beneficiaries');
    }
};