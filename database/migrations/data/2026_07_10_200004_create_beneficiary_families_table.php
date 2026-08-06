<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beneficiary_families', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('beneficiary_uuid')
                ->constrained('beneficiaries', 'uuid')
                ->onDelete('CASCADE');
            $table->text('name');
            $table->foreignId('sex_id')->constrained('sexes');
            $table->integer('age');
            $table->foreignId('civil_id')
                ->constrained('civil_statuses')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->foreignId('relationship_id')
                ->constrained('relationships')
                ->onDelete('CASCADE')
                ->onUpdate('CASCADE');
            $table->text('occupation')->nullable();
            $table->text('income')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('client_beneficiary_families');
    }
};
