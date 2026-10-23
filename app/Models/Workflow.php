<?php

namespace App\Models;

use App\Services\ActivityLoggerService;
use App\Traits\HasUuid;
use Database\Factories\WorkflowFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use LogicException;

class Workflow extends Model
{
    /** @use HasFactory<WorkflowFactory> */
    use HasFactory, HasUuid, SoftDeletes;

    protected $fillable = ['uuid', 'name', 'description', 'is_active', 'workflow_group_uuid',
        'version_number', 'published_at', 'created_by'];

    protected $attributes = ['is_active' => true, 'version_number' => 1];

    protected $casts = ['is_active' => 'boolean', 'version_number' => 'integer', 'published_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(function (Workflow $workflow) {
            $workflow->workflow_group_uuid ??= $workflow->uuid;
        });
        static::updating(function (Workflow $workflow) {
            $published = DB::table('workflows')->where('uuid', $workflow->getOriginal('uuid'))->value('published_at');
            if ($published && array_diff(array_keys($workflow->getDirty()), ['is_active', 'updated_at'])) {
                throw new LogicException('Published workflow definitions are immutable. Publish a new version.');
            }
        });
        static::updated(function (Workflow $workflow) {
            if ($workflow->wasChanged('is_active')) {
                ActivityLoggerService::logSuccess('Changed workflow version availability', [
                    'workflow_uuid' => $workflow->uuid, 'is_active' => $workflow->is_active,
                ]);
            }
        });
        static::deleting(fn () => throw new LogicException('Workflow versions cannot be deleted. Change availability instead.'));
    }

    /** @return HasMany<WorkflowStage, $this> */
    public function stages(): HasMany
    {
        return $this->hasMany(WorkflowStage::class, 'workflow_uuid', 'uuid');
    }

    /** @return HasMany<FuneralAssistanceType, $this> */
    public function funeralAssistanceTypes(): HasMany
    {
        return $this->hasMany(FuneralAssistanceType::class, 'workflow_uuid', 'uuid');
    }

    /** @return HasMany<Workflow, $this> */
    public function versions(): HasMany
    {
        return $this->hasMany(self::class, 'workflow_group_uuid', 'workflow_group_uuid')->orderByDesc('version_number');
    }

    /** @param Builder<Workflow> $query
     * @return Builder<Workflow>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('workflows.published_at');
    }

    /** @param Builder<Workflow> $query
     * @return Builder<Workflow>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('workflows.is_active', true);
    }

    /** @param Builder<Workflow> $query
     * @return Builder<Workflow>
     */
    public function scopeLatestVersion(Builder $query): Builder
    {
        return $query->published()->whereNotExists(function ($newer) {
            $newer->selectRaw('1')->from('workflows as newer')
                ->whereColumn('newer.workflow_group_uuid', 'workflows.workflow_group_uuid')
                ->whereColumn('newer.version_number', '>', 'workflows.version_number')
                ->whereNotNull('newer.published_at');
        });
    }

    /** @param Builder<Workflow> $query
     * @return Builder<Workflow>
     */
    public function scopeSelectable(Builder $query): Builder
    {
        return $query->latestVersion()->active()->whereHas('stages')
            ->whereDoesntHave('stages', fn ($stages) => $stages->whereNull('position')->orWhere('position', '<', 1)
                ->orWhereDoesntHave('permission', fn ($permissions) => $permissions->where('guard_name', 'web')
                    ->where(fn ($names) => $names->where('name', 'like', 'Workflow %')->orWhere('name', 'like', 'workflow.%'))))
            ->whereRaw('(select count(*) from workflow_stages s where s.workflow_uuid = workflows.uuid and s.deleted_at is null) = (select max(position) from workflow_stages s where s.workflow_uuid = workflows.uuid and s.deleted_at is null)');
    }

    public function isLatestVersion(): bool
    {
        return self::latestVersion()->whereKey($this->uuid)->exists();
    }

    public function isSelectable(): bool
    {
        return self::selectable()->whereKey($this->uuid)->exists();
    }

    public function label(): string
    {
        return $this->name.' · v'.$this->version_number;
    }

    public static function relations(): array
    {
        return ['stages', 'funeralAssistanceTypes'];
    }

    public function scopeIndex($query)
    {
        return $query->latestVersion()->withCount('funeralAssistanceTypes');
    }
}
