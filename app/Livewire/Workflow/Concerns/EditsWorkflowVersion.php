<?php

namespace App\Livewire\Workflow\Concerns;

use App\Models\Application;
use App\Models\Workflow;
use App\Services\WorkflowVersionService;
use Illuminate\Support\Js;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Spatie\Permission\Models\Permission;

trait EditsWorkflowVersion
{
    public ?Workflow $workflow = null;

    public string $name = '';

    public string $description = '';

    public array $stages = [];

    public array $removedStages = [];

    public array $stagePositions = [];

    #[Locked]
    public array $workflowPermissions = [];

    #[Locked]
    public bool $historical = false;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    protected function initializeEditor(?Workflow $workflow = null): void
    {
        $this->workflow = $workflow;
        $this->name = $workflow ? $workflow->name : '';
        $this->description = $workflow ? $workflow->description : '';
        $this->historical = $workflow !== null && ! $workflow->isLatestVersion();
        $this->removedStages = [];
        $this->stages = $workflow ? $workflow->stages()->orderBy('position')->get()->map(fn ($stage) => [
            'key' => (string) Str::uuid(), 'name' => $stage->name, 'description' => $stage->description,
            'permission_id' => (string) $stage->permission_id,
        ])->all() : [];
        $this->workflowPermissions = Permission::where('guard_name', 'web')
            ->where(fn ($query) => $query->where('name', 'like', 'Workflow %')->orWhere('name', 'like', 'workflow.%'))
            ->orderBy('name')->pluck('name', 'id')->all();
        $this->syncPositions();
        if (! $workflow) {
            $this->addStage();
        }
    }

    protected function assertDraft(): void
    {
        abort_if($this->historical, 403, 'Historical versions are read-only.');
    }

    public function addStage(): void
    {
        $this->assertDraft();
        $this->stages[] = ['key' => (string) Str::uuid(), 'name' => '', 'description' => '', 'permission_id' => ''];
        $this->syncPositions();
    }

    public function removeStage(string $key): void
    {
        $this->assertDraft();
        foreach ($this->stages as $index => $stage) {
            if ($stage['key'] === $key) {
                $this->removedStages[$key] = $stage;
                array_splice($this->stages, $index, 1);
                break;
            }
        }
        $this->syncPositions();
    }

    public function restoreStage(string $key): void
    {
        $this->assertDraft();
        if (isset($this->removedStages[$key])) {
            $this->stages[] = $this->removedStages[$key];
            unset($this->removedStages[$key]);
        }
        $this->syncPositions();
    }

    public function moveStage(string $key, int $direction): void
    {
        $this->assertDraft();
        if (! in_array($direction, [-1, 1], true)) {
            abort(422);
        }
        $index = array_search($key, array_column($this->stages, 'key'), true);
        if ($index !== false && isset($this->stages[$index + $direction])) {
            $stage = $this->stages[$index];
            array_splice($this->stages, $index, 1);
            array_splice($this->stages, $index + $direction, 0, [$stage]);
        }
        $this->syncPositions();
    }

    public function updatedStagePositions($value, string $key): void
    {
        $this->assertDraft();
        if (! filter_var($value, FILTER_VALIDATE_INT) || $value < 1 || $value > count($this->stages)) {
            throw ValidationException::withMessages(['stagePositions.'.$key => 'Choose a position within the draft stages.']);
        }
        $index = array_search($key, array_column($this->stages, 'key'), true);
        if ($index !== false) {
            $stage = $this->stages[$index];
            array_splice($this->stages, $index, 1);
            array_splice($this->stages, (int) $value - 1, 0, [$stage]);
        }
        $this->syncPositions();
    }

    protected function syncPositions(): void
    {
        $this->stagePositions = [];
        foreach ($this->stages as $index => $stage) {
            $this->stagePositions[$stage['key']] = $index + 1;
        }
    }

    public function save(): void
    {
        $this->assertDraft();
        $oldUuid = $this->workflow?->uuid;
        $workflow = app(WorkflowVersionService::class)->publish([
            'name' => $this->name, 'description' => $this->description, 'stages' => $this->stages,
        ], $this->workflow);
        $this->initializeEditor($workflow);
        $this->resetValidation();
        $this->js('window.history.replaceState({}, "", '.Js::from(route('workflows.edit', $workflow)).')');
        $this->dispatch('notification:toast', ['type' => 'success',
            'text' => $oldUuid === $workflow->uuid ? 'No definition changes to publish.' : 'Workflow version '.$workflow->version_number.' published.']);
    }

    public function setAvailability(string $uuid, bool $active): void
    {
        abort_unless($this->workflow !== null, 404);
        $version = $this->workflow->versions()->whereKey($uuid)->firstOrFail();
        app(WorkflowVersionService::class)->setAvailability($version, $active);
        $this->workflow->refresh();
        $this->dispatch('notification:toast', ['type' => 'success', 'text' => $active ? 'Version resumed.' : 'Version paused.']);
    }

    public function render()
    {
        $versions = $this->workflow ? $this->workflow->versions()->published()->get() : collect();
        $counts = $versions->isEmpty() ? collect() : Application::query()
            ->join('recommendations as run', 'run.uuid', '=', 'applications.current_recommendation_uuid')
            ->whereIn('run.workflow_uuid', $versions->pluck('uuid'))
            ->whereDoesntHave('closure')->whereDoesntHave('rejection')
            ->whereDoesntHave('cancellation')->whereDoesntHave('referral')
            ->groupBy('run.workflow_uuid')->selectRaw('run.workflow_uuid, count(*) as total')
            ->pluck('total', 'run.workflow_uuid');

        return view('livewire.workflow.edit', ['versions' => $versions, 'runCounts' => $counts]);
    }
}
