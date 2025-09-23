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
        Schema::create('deceased', function (Blueprint $table) {
            $table->id();

            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->date('date_of_birth');
            $table->date('date_of_death');
            $table->foreignId('gender')
                ->constrained('sexes')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            // $table->string('address');
            // $table->foreignId('barangay_id')
            //     ->constrained('barangays')
            //     ->onDelete('cascade')
            //     ->onUpdate('cascade');
            // TODO: Religion column

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deceased');
    }
};
