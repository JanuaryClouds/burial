<?php

use App\Services\WorkflowVersionAudit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Audit before DDL: MySQL schema changes cannot be transactionally rolled back.
        $audit = app(WorkflowVersionAudit::class)->inspect();
        if ($audit['errors']) {
            throw new RuntimeException(implode("\n", $audit['errors']));
        }
        Schema::table('workflows', function (Blueprint $table) {
            $table->uuid('workflow_group_uuid')->nullable()->index();
            $table->unsignedInteger('version_number')->default(1);
            $table->timestamp('published_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unique(['workflow_group_uuid', 'version_number'], 'workflows_group_version_unique');
            $table->index('name', 'workflows_name_index');
        });
        Schema::create('workflow_name_reservations', function (Blueprint $table) {
            $table->string('name')->primary();
            $table->uuid('workflow_group_uuid')->index();
        });
        Schema::table('recommendations', function (Blueprint $table) {
            $table->foreignUuid('workflow_uuid')->nullable()->constrained('workflows', 'uuid')->restrictOnDelete();
        });
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignUuid('current_recommendation_uuid')->nullable()
                ->constrained('recommendations', 'uuid')->restrictOnDelete();
        });
        DB::transaction(function () use ($audit) {
            DB::table('workflows')->update([
                'workflow_group_uuid' => DB::raw('uuid'),
                'version_number' => 1, 'published_at' => now(),
            ]);
            foreach (DB::table('workflows')->get(['name', 'workflow_group_uuid']) as $workflow) {
                DB::table('workflow_name_reservations')->insertOrIgnore([
                    'name' => mb_strtolower(trim($workflow->name)), 'workflow_group_uuid' => $workflow->workflow_group_uuid,
                ]);
            }
            foreach ($audit['recommendations'] as $uuid => $workflowUuid) {
                DB::table('recommendations')->where('uuid', $uuid)->update(['workflow_uuid' => $workflowUuid]);
            }
            foreach ($audit['applications'] as $uuid => $recommendationUuid) {
                DB::table('applications')->where('uuid', $uuid)->update(['current_recommendation_uuid' => $recommendationUuid]);
            }
        });
        Schema::table('workflows', fn (Blueprint $table) => $table->uuid('workflow_group_uuid')->nullable(false)->change());

        // Preserve history instead of allowing cascade deletion or nulling snapshot references.
        $this->replaceForeign('workflow_stages', 'workflow_uuid', 'workflows');
        $this->replaceForeign('workflow_stages', 'permission_id', 'permissions', 'id');
        $this->replaceForeign('applications', 'current_workflow_stage_uuid', 'workflow_stages');
        $this->replaceForeign('workflow_histories', 'from_stage_uuid', 'workflow_stages');
        $this->replaceForeign('workflow_histories', 'to_stage_uuid', 'workflow_stages');
        $this->replaceForeign('workflow_histories', 'recommendation_uuid', 'recommendations');
        $this->replaceForeign('recommendations', 'application_uuid', 'applications');
    }

    private function replaceForeign(string $tableName, string $column, string $target, string $key = 'uuid'): void
    {
        Schema::table($tableName, function (Blueprint $table) use ($column, $target, $key) {
            $table->dropForeign([$column]);
            $table->foreign($column)->references($key)->on($target)->restrictOnDelete()->restrictOnUpdate();
        });
    }

    public function down(): void
    {
        throw new RuntimeException('Workflow versioning preserves historical references. Restore a pre-rollout backup or apply a forward migration; destructive rollback is disabled.');
    }
};
