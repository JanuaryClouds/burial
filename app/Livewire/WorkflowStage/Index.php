<?php

namespace App\Livewire\WorkflowStage;

use App\Models\Workflow;
use App\Models\WorkflowStage;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public Workflow $workflow;
    public Collection $stages;

    public function mount(Workflow $workflow): void
    {
        $this->workflow = $workflow;
        $this->queryStages();
    }

    #[On('refreshWorkflow')]
    public function queryStages(): void
    {
        $this->stages = $this->workflow
            ->stages()
            ->orderBy('position')
            ->get();
    }

    public function addStage(): void
    {
        WorkflowStage::create([
            'workflow_uuid' => $this->workflow->uuid,
            'name' => 'New Stage',
            'description' => 'Stage Description',
            'position' => $this->stages->count() + 1,
        ]);

        $this->dispatch('refreshWorkflow');
    }

    public function render()
    {
        return view('livewire.workflow-stage.index', [
            'stages' => $this->stages,
        ]);
    }
}
