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
        Schema::create('funeral_assistances', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->foreignUuid('client_id')
                ->constrained('clients', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('forwarded_at')->nullable();
            $table->string('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('funeral_assistance');
    }
};
