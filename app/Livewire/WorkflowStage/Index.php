<?php

namespace App\Livewire\WorkflowStage;

use App\Models\Workflow;
use App\Models\WorkflowStage;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Index extends Component
{
    public Workflow $workflow;

    public function mount(Workflow $workflow)
    {
        $this->workflow = $workflow;
    }

    public function queryStages()
    {
        if ($this->workflow->stages()->count() === 0) {
            return collect();
        }

        $stage = $this->workflow->stages()
            ->whereDoesntHave('incomingStages')
            ->first();

        $orderedStages = collect();

        while ($stage) {
            $orderedStages->push($stage);
            $stage = $stage->outgoingStages
                ->first()
                ?->toStage;
        }

        return $orderedStages;
    }

    public function render()
    {
        return view('livewire.workflow-stage.index', [
            'stages' => $this->queryStages(),
        ]);
    }
}
