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
        Schema::create('burial_assistance_requests', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('deceased_firstname');
            $table->string('deceased_lastname');
            $table->string('representative');
            $table->string('representative_phone');
            $table->string('representative_email')->nullable();
            $table->foreignId('representative_relationship')
                ->constrained('relationships')
                ->noActionOnDelete()
                ->noActionOnUpdate();
            $table->string('burial_address');
            $table->foreignId('barangay_id')
                ->constrained('barangays')
                ->noActionOnDelete()
                ->noActionOnUpdate();
            $table->datetime('start_of_burial');
            $table->datetime('end_of_burial');
            $table->enum('status', [
                'pending',
                'approved',
                'rejected',
            ])->default('pending');
            $table->foreignId('type_of_assistance')
                ->default(8)
                ->constrained('assistances')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string('remarks')->nullable()->max(255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistance_request_forms');
    }
};
