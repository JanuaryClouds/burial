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
        Schema::create('claimants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('client_id')
                ->nullable()
                ->constrained('clients')
                ->onDelete('CASCADE')
                ->onUpdate('cascade');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->foreignId('relationship_to_deceased')
                ->constrained('relationships')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->string('mobile_number')->nullable();
            $table->string('address');
            $table->foreignId('barangay_id')
                ->constrained('barangays')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('claimants');
    }
};
