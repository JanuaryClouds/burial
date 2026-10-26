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
        $this->stages = $workflow ? $workflow->stages()->with('customFields')->orderBy('position')->get()->map(fn ($stage) => [
            'key' => (string) Str::uuid(), 'name' => $stage->name, 'description' => $stage->description,
            'permission_id' => (string) $stage->permission_id,
            'custom_fields' => $stage->customFields->map(fn ($field) => array_merge(
                $field->only(['name', 'label', 'help_text', 'input_type', 'required']), ['key' => (string) Str::uuid()]))->all(),
            'removed_fields' => [],
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
        $this->stages[] = ['key' => (string) Str::uuid(), 'name' => '', 'description' => '', 'permission_id' => '',
            'custom_fields' => [], 'removed_fields' => []];
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

    private function draftStageIndex(string $stageKey): int
    {
        $this->assertDraft();
        $index = array_search($stageKey, array_column($this->stages, 'key'), true);
        abort_if($index === false, 404);

        return $index;
    }

    public function addField(string $stageKey): void
    {
        $index = $this->draftStageIndex($stageKey);
        $this->stages[$index]['custom_fields'][] = ['key' => (string) Str::uuid(), 'name' => '',
            'label' => '', 'help_text' => '', 'input_type' => 'text', 'required' => true];
    }

    public function updatedStages($value, string $path): void
    {
        $this->assertDraft();
        if (preg_match('/^(\d+)\.custom_fields\.(\d+)\.label$/', $path, $matches)) {
            $field = &$this->stages[(int) $matches[1]]['custom_fields'][(int) $matches[2]];
            if (blank($field['name'] ?? null) && is_string($value)) {
                $field['name'] = Str::slug($value, '_');
            }
        }
    }

    public function removeField(string $stageKey, string $fieldKey): void
    {
        $index = $this->draftStageIndex($stageKey);
        foreach ($this->stages[$index]['custom_fields'] as $position => $field) {
            if ($field['key'] === $fieldKey) {
                $this->stages[$index]['removed_fields'][$fieldKey] = $field;
                array_splice($this->stages[$index]['custom_fields'], $position, 1);
                break;
            }
        }
    }

    public function restoreField(string $stageKey, string $fieldKey): void
    {
        $index = $this->draftStageIndex($stageKey);
        if (isset($this->stages[$index]['removed_fields'][$fieldKey])) {
            $this->stages[$index]['custom_fields'][] = $this->stages[$index]['removed_fields'][$fieldKey];
            unset($this->stages[$index]['removed_fields'][$fieldKey]);
        }
    }

    public function moveField(string $stageKey, string $fieldKey, int $direction): void
    {
        $index = $this->draftStageIndex($stageKey);
        abort_unless(in_array($direction, [-1, 1], true), 422);
        $fields = &$this->stages[$index]['custom_fields'];
        $position = array_search($fieldKey, array_column($fields, 'key'), true);
        if ($position !== false && isset($fields[$position + $direction])) {
            $field = $fields[$position];
            array_splice($fields, $position, 1);
            array_splice($fields, $position + $direction, 0, [$field]);
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
