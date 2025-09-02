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
        Schema::create('claimant_changes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('burial_assistance_id')
                ->constrained('burial_assistances')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('old_claimant_id')
                ->nullable()
                ->constrained('claimants')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('new_claimant_id')
                ->constrained('claimants')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->dateTime('changed_at');
            $table->text('reason_for_change')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claimant_changes');
    }
};
