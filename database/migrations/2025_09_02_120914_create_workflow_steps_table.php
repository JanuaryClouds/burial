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
        Schema::create('workflow_steps', function (Blueprint $table) {
            $table->id();

            $table->integer('order_no');
            $table->foreignId('handler_id')
                  ->constrained('handlers')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
            $table->boolean('requires_extra_data')->default(false);
            $table->boolean('is_optional')->default(false);
            $table->json('extra_data_schema')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workflow_steps');
    }
};
