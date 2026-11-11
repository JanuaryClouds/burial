<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_document_files', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('application_uuid')->constrained('applications', 'uuid')->restrictOnDelete();
            $table->foreignId('document_requirement_id')->constrained('document_requirements')->restrictOnDelete();
            $table->string('storage', 16);
            $table->string('folder', 200);
            $table->string('file_name', 200);
            $table->timestamps();
            $table->unique(['application_uuid', 'document_requirement_id', 'storage'], 'application_document_storage_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('application_document_files');
    }
};
