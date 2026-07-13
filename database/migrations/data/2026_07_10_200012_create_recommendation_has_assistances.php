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
        Schema::create('recommendation_has_assistances', function (Blueprint $table) {
            $table->foreignUuid('recommendation_uuid')
                ->constrained('recommendations', 'uuid')
                ->cascadeOnDelete();
            $table->foreignUuid('funeral_assistance_uuid')
                ->constrained('funeral_assistance_types', 'uuid')
                ->cascadeOnDelete();
            $table->primary([
                'recommendation_uuid',
                'funeral_assistance_uuid'
            ]);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recommendation_has_assistances');
    }
};
