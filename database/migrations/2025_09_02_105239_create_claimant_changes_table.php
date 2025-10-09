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

            $table->foreignUuid('burial_assistance_id')
                ->constrained('burial_assistances', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignUuid('old_claimant_id')
                ->nullable()
                ->constrained('claimants', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignUuid('new_claimant_id')
                ->constrained('claimants', 'id')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->dateTime('changed_at')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
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
