<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('application_submission_drafts', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignUuid('client_uuid')->constrained('clients', 'uuid')->restrictOnDelete();
            $table->foreignUuid('beneficiary_uuid')->constrained('beneficiaries', 'uuid')->restrictOnDelete();
            $table->unsignedBigInteger('relationship_id');
            $table->unsignedInteger('version')->default(1);
            $table->text('documents')->nullable();
            $table->foreignUuid('application_uuid')->nullable()->constrained('applications', 'uuid')->restrictOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'client_uuid', 'beneficiary_uuid'], 'citizen_submission_identity');
        });
        Schema::create('interview_scheduling_requests', function (Blueprint $table): void {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('client_uuid')->constrained('clients', 'uuid')->restrictOnDelete();
            $table->uuid('claimant_assignment_uuid')->nullable()->index();
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->dateTime('schedule');
            $table->text('snapshot');
            $table->string('status')->default('pending');
            $table->foreignUuid('interview_uuid')->nullable()->constrained('interviews', 'uuid')->restrictOnDelete();
            $table->timestamps();
        });
        Schema::create('interview_notification_attempts', function (Blueprint $table): void {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignUuid('request_uuid')->constrained('interview_scheduling_requests', 'uuid')->restrictOnDelete();
            $table->string('channel');
            $table->string('status')->default('pending');
            $table->uuid('claim')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->string('outcome')->nullable();
            $table->timestamps();
            $table->index(['request_uuid', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interview_notification_attempts');
        Schema::dropIfExists('interview_scheduling_requests');
        Schema::dropIfExists('application_submission_drafts');
    }
};
