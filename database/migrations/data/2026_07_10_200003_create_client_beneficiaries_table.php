<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->foreignId('sex_id')->constrained('sexes');
            $table->foreignId('religion_id')->constrained('religions')->onDelete('cascade')->onUpdate('cascade');
            $table->date('date_of_birth');
            $table->date('date_of_death');
            $table->boolean('pwd')->default(false);
            $table->string('region_code');
            $table->string('province_code')->nullable();
            $table->string('municipality_code')->nullable();
            $table->string('barangay_code');
            $table->text('street');
            $table->text('house_no');
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_beneficiaries');
    }
};
