<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('CASCADE');
            $table->string('first_name');
            $table->string('middle_name');
            $table->string('last_name');
            $table->foreignId('sex_id')->constrained('sexes');
            $table->string('date_of_birth');
            $table->string('place_of_birth');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('client_beneficiaries');
    }
};