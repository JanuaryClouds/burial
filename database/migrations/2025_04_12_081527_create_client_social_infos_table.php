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
        Schema::create('client_social_infos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')->constrained('clients')->onDelete('CASCADE');
            $table->foreignId('relationship_id')->constrained('relationships');
            $table->foreignId('civil_id')->constrained('civil_statuses');
            $table->foreignId('education_id')->constrained('educations');
            $table->string('income');
            $table->string('philhealth');
            $table->string('skill');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_social_infos');
    }
};