<?php

namespace App\Models;

use App\Traits\HasCustomFields;
use App\Traits\HasUuid;
use Database\Factories\WorkflowStageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Permission\Models\Permission;

class WorkflowStage extends Model
{
    /** @use HasFactory<WorkflowStageFactory> */
    use HasCustomFields, HasFactory, HasUuid, SoftDeletes;

    protected $fillable = ['name', 'position', 'description', 'workflow_uuid', 'permission_id'];

    protected $casts = ['position' => 'integer'];

    protected static function booted(): void
    {
        static::creating(function (WorkflowStage $stage) {
            $stage->assertMutableParent($stage->workflow_uuid);
            $stage->position ??= (self::where('workflow_uuid', $stage->workflow_uuid)->max('position') ?? 0) + 1;
        });
        static::updating(function (WorkflowStage $stage) {
            $stage->assertMutableParent($stage->getOriginal('workflow_uuid'));
            $stage->assertMutableParent($stage->workflow_uuid);
        });
        static::deleting(fn (WorkflowStage $stage) => $stage->assertMutableParent($stage->workflow_uuid));
        static::restoring(fn (WorkflowStage $stage) => $stage->assertMutableParent($stage->workflow_uuid));
    }

    private function assertMutableParent(string $uuid): void
    {
        if (Workflow::withTrashed()->whereKey($uuid)->value('published_at')) {
            throw new \LogicException('Published stages are immutable. Edit a draft and publish a new version.');
        }
    }

    /** @return HasMany<Application, $this> */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'current_workflow_stage_uuid', 'uuid');
    }

    /** @return BelongsTo<Workflow, $this> */
    public function workflow(): BelongsTo
    {
        return $this->belongsTo(Workflow::class, 'workflow_uuid', 'uuid')->withTrashed();
    }

    /** @return HasMany<WorkflowHistory, $this> */
    public function nextStages(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'to_stage_uuid', 'uuid');
    }

    /** @return HasMany<WorkflowHistory, $this> */
    public function previousStages(): HasMany
    {
        return $this->hasMany(WorkflowHistory::class, 'from_stage_uuid', 'uuid');
    }

    /** @return BelongsTo<Permission, $this> */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class, 'permission_id', 'id');
    }
}
