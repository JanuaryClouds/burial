<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('client_beneficiary_families', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('CASCADE');
            $table->string('name');
            $table->foreignId('sex_id')->constrained('sexes');
            $table->integer('age');
            $table->foreignId('civil_id')->constrained('civil_statuses');
            $table->foreignId('relationship_id')->constrained('relationships');
            $table->string('occupation');
            $table->string('income');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('client_beneficiary_families');
    }
};