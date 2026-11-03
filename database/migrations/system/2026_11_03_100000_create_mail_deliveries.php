<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mail_deliveries', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->text('recipient');
            $table->longText('payload')->nullable();
            $table->string('status')->default('pending')->index();
            $table->string('failure_code')->nullable();
            $table->unsignedInteger('attempt_count')->default(0);
            $table->uuid('claim')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
        Schema::create('mail_delivery_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mail_delivery_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->uuid('claim')->unique();
            $table->string('status');
            $table->string('failure_code')->nullable();
            $table->timestamp('started_at');
            $table->timestamp('finished_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mail_delivery_attempts');
        Schema::dropIfExists('mail_deliveries');
    }
};
