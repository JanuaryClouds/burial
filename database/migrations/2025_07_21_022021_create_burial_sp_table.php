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
        Schema::create('burial_service_providers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_details');
            $table->string('address');
            $table->string('remarks')->nullable()->max(255);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('burial_sp');
    }
};
