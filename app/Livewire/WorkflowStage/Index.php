<?php

namespace App\Livewire\WorkflowStage;

use App\Models\Permission;
use App\Models\Workflow;
use App\Models\WorkflowStage;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public Workflow $workflow;
    public Collection $stages;
    public Collection $trashedStages;
    public Collection $workflowPermissions;

    public function mount(Workflow $workflow): void
    {
        $this->workflow = $workflow;
        $this->workflowPermissions = Permission::where('name', 'like', 'workflow%')
            ->get()
            ->mapWithKeys(function (Permission $permission) {
                return [$permission->id => Str::remove(['workflow.', '.update'], $permission->name)];
            });

        $this->queryStages();
    }

    #[On('refreshWorkflow')]
    public function queryStages(): void
    {
        $this->stages = WorkflowStage::where('workflow_uuid', $this->workflow->uuid)
            ->orderBy('position')
            ->get();

        $this->trashedStages = WorkflowStage::where('workflow_uuid', $this->workflow->uuid)
            ->onlyTrashed()
            ->orderBy('position')
            ->get();
    }

    public function addStage(): void
    {
        WorkflowStage::create([
            'workflow_uuid' => $this->workflow->uuid,
            'name' => 'New Stage',
            'description' => 'Stage Description',
        ]);

        $this->dispatch('refreshWorkflow');
    }

    public function render()
    {
        return view('livewire.workflow-stage.index');
    }
}
