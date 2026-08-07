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
        Schema::create('workflows', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->string('name');
            $table->text('description');
            $table->timestamps();
        });

        Schema::table('funeral_assistance_types', function (Blueprint $table) {
            $table->foreignUuid('workflow_uuid')
                ->nullable()
                ->constrained('workflows', 'uuid')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('funeral_assistance_types', function (Blueprint $table) {
            $table->dropForeign(['workflow_uuid']);
            $table->dropColumn('workflow_uuid');
        });
        
        Schema::dropIfExists('workflows');
    }
};
