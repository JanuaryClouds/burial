<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('document_templates', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('funeral_assistance_type_uuid')->constrained('funeral_assistance_types', 'uuid')->restrictOnDelete();
            $table->string('name');
            $table->string('path');
            $table->string('original_filename');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('document_templates');
    }
};
