<?php

namespace App\Livewire\WorkflowStage;

use App\Models\Workflow;
use App\Models\WorkflowStage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use App\Models\Permission;
use Illuminate\Support\Collection;

class Show extends Component
{
    public WorkflowStage $stage;
    public int $maxCount;
    public Collection $workflowPermissions;

    #[Validate('required|string|max:100')]
    public string $name;

    #[Validate('required|string|max:65535')]
    public string $description;

    #[Validate('required|exists:permissions,id')]
    public string $permission_id;

    public function mount(WorkflowStage $stage, int $maxCount)
    {
        $this->stage = $stage;
        $this->maxCount = $maxCount;
        $this->name = $this->stage->name;
        $this->description = $this->stage->description;
        $this->permission_id = $this->stage->permission_id;
        $this->workflowPermissions = $this->loadPermissions();
    }

    private function loadPermissions(): Collection
    {
        return Permission::where('name', 'like', '%workflow%')
            ->get()
            ->mapWithKeys(function (Permission $permission) {
                return [$permission->id => Str::remove(['workflow.', '.update'], $permission->name)];
            });
    }

    #[On('refreshWorkflow')]
    public function loadStage()
    {
        $this->stage->refresh();
        $this->maxCount = $this->stage->workflow->stages()->count();
        $this->name = $this->stage->name;
        $this->description = $this->stage->description;
        $this->permission_id = $this->stage->permission_id;
        $this->workflowPermissions = $this->loadPermissions();
    }

    public function save(string $uuid)
    {
        $validated = $this->validate();

        WorkflowStage::where('uuid', $uuid)
            ->update($validated);

        $this->dispatch('refreshWorkflow');
        session()->flash('Successfully updated stage information');
    }

    public function delete(string $uuid)
    {
        DB::transaction(function () use ($uuid) {
            $stage = WorkflowStage::where('uuid', $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            $emptyPosition = $stage->position;

            $stage->update([
                'position' => null,
                'permission_id' => null
            ]);

            $stage->delete();

            WorkflowStage::where('workflow_uuid', $stage->workflow_uuid)
                ->where('position', '>', $emptyPosition)
                ->decrement('position');
        });

        $this->dispatch('refreshWorkflow');
        session()->flash('Successfully deleted stage');
    }

    public function moveUp(string $uuid)
    {
        DB::transaction(function () use ($uuid) {
            $stage = WorkflowStage::where('uuid', $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            if ($stage->position <= 1) {
                return;
            }

            $previous = WorkflowStage::where('workflow_uuid', $stage->workflow_uuid)
                ->where('position', $stage->position - 1)
                ->lockForUpdate()
                ->firstOrFail();

            $currentposition = $stage->position;
            $previousPosition = $previous->position;

            $stage->update(['position' => null]);
            $previous->update(['position' => $currentposition]);
            $stage->update(['position' => $previousPosition]);
        });

        $this->save($uuid);
    }

    public function moveDown(string $uuid)
    {
        DB::transaction(function () use ($uuid) {
            $stage = WorkflowStage::where('uuid', $uuid)
                ->lockForUpdate()
                ->firstOrFail();

            if ($stage->position >= Workflow::where('uuid', $stage->workflow_uuid)->first()->stages()->count()) {
                return;
            }

            $next = WorkflowStage::where('workflow_uuid', $stage->workflow_uuid)
                ->where('position', $stage->position + 1)
                ->lockForUpdate()
                ->firstOrFail();

            $currentposition = $stage->position;
            $nextPosition = $next->position;

            $stage->update(['position' => null]);
            $next->update(['position' => $currentposition]);
            $stage->update(['position' => $nextPosition]);
        });

        $this->save($uuid);
    }

    public function render()
    {
        return view('livewire.workflow-stage.show');
    }
}
