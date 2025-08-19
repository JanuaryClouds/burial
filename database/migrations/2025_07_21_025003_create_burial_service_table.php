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
        Schema::create('burial_services', function (Blueprint $table) {
            $table->id();
            $table->string('deceased_firstname');
            $table->string('deceased_lastname');
            $table->string('representative');
            $table->string('representative_contact');
            $table->foreignId('rep_relationship')
                ->constrained('relationships')
                ->noActionOnDelete()
                ->noActionOnUpdate();
            $table->string('burial_address');
            $table->datetime('start_of_burial');
            $table->datetime('end_of_burial');
            $table->foreignId('burial_service_provider')
                ->nullable()
                ->constrained('burial_service_providers')
                ->noActionOnDelete()
                ->noActionOnUpdate();
            $table->string('collected_funds');
            $table->string('remarks')->nullable()->max(255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('burial_service');
    }
};
