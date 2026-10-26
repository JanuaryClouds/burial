<?php

use App\Models\ApplicationClaimantAssignment;
use App\Models\Client;
use App\Services\ClaimantAssignmentAudit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL DDL is not transactional: audit before making any schema changes.
        $errors = app(ClaimantAssignmentAudit::class)->inspect();
        if ($errors) {
            throw new RuntimeException(implode("\n", $errors));
        }
        Schema::create('application_claimant_assignments', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignUuid('application_uuid')->constrained('applications', 'uuid')->restrictOnDelete();
            $table->foreignUuid('client_uuid')->unique()->constrained('clients', 'uuid')->restrictOnDelete();
            $table->foreignId('relationship_id')->nullable()->constrained('relationships')->restrictOnDelete();
            $table->unsignedInteger('sequence');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->foreignId('assigned_by')->nullable()->constrained('users')->restrictOnDelete();
            $table->text('reason')->nullable();
            $table->text('identity_snapshot');
            $table->timestamps();
            $table->unique(['application_uuid', 'sequence'], 'claimant_application_sequence_unique');
        });
        Schema::table('applications', fn (Blueprint $table) => $table->foreignUuid('current_claimant_assignment_uuid')->nullable()
            ->constrained('application_claimant_assignments', 'uuid')->restrictOnDelete());
        foreach (['interviews', 'assessments', 'recommendations'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name) {
                $column = $table->foreignUuid('claimant_assignment_uuid')->nullable();
                if ($name === 'assessments') {
                    $column->unique();
                }
                $column->constrained('application_claimant_assignments', 'uuid')->restrictOnDelete();
            });
        }
        Schema::table('system_settings', fn (Blueprint $table) => $table->unsignedInteger('claimant_change_limit')->default(1));

        DB::transaction(function () {
            DB::table('applications')->orderBy('uuid')->each(function ($application) {
                $assignment = ApplicationClaimantAssignment::create([
                    'application_uuid' => $application->uuid,
                    'client_uuid' => $application->client_uuid,
                    'relationship_id' => $application->relationship_id,
                    'sequence' => 1,
                    'started_at' => $application->created_at ?? now(),
                    'identity_snapshot' => ApplicationClaimantAssignment::snapshot(Client::findOrFail($application->client_uuid)),
                ]);
                DB::table('applications')->where('uuid', $application->uuid)->update(['current_claimant_assignment_uuid' => $assignment->uuid]);
                foreach (['assessments', 'recommendations'] as $name) {
                    DB::table($name)->where('application_uuid', $application->uuid)->update(['claimant_assignment_uuid' => $assignment->uuid]);
                }
                DB::table('interviews')->where('client_uuid', $application->client_uuid)->update(['claimant_assignment_uuid' => $assignment->uuid]);
            });
        });
    }

    public function down(): void
    {
        if (DB::table('application_claimant_assignments')->where('sequence', '>', 1)->exists()) {
            throw new RuntimeException('Claimant replacements exist. Restore a verified backup instead of discarding history.');
        }
        foreach (['interviews', 'assessments', 'recommendations'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name) {
                $table->dropForeign(['claimant_assignment_uuid']);
                if ($name === 'assessments') {
                    $table->dropUnique(['claimant_assignment_uuid']);
                }
                $table->dropColumn('claimant_assignment_uuid');
            });
        }
        Schema::table('applications', fn (Blueprint $table) => $table->dropConstrainedForeignId('current_claimant_assignment_uuid'));
        Schema::dropIfExists('application_claimant_assignments');
        Schema::table('system_settings', fn (Blueprint $table) => $table->dropColumn('claimant_change_limit'));
    }
};
