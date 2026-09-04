<?php

namespace App\Livewire\Workflow\History;

use App\Models\Application;
use App\Models\WorkflowHistory;
use App\Models\WorkflowStage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    public Application $application;
    
    public ?WorkflowStage $toStage;
    
    public Collection $stages;
    
    public bool $revertToRecommendation = false;
    
    public bool $showDateOut = false;
    
    public bool $showForm = false;
    
    #[Rule('required|date')]
    public string $dateIn;

    #[Rule('required|uuid|exists:workflow_stages,uuid')]
    public ?string $toStageUuid;

    #[Rule('required|date|after_or_equal:dateIn')]
    public ?string $dateOut;

    #[Rule('nullable|string')]
    public ?string $reason;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->stages = $this->loadStages($application);

        if ($this->stages) {
            $this->showForm = Auth::user()->can($this->application->toStage()->permission->name);
        }
    }
    
    #[On('refreshWorkflowHistory')]
    public function refresh()
    {
        $this->reset('dateIn', 'dateOut', 'reason', 'toStageUuid');
        $this->application->fresh();
        $this->stages = $this->loadStages($this->application);

        if ($this->stages) {
            $this->showForm = Auth::user()->can($this->application->toStage()->permission->name);
        }
    }

    private function loadStages(Application $application): Collection
    {
        return $application->currentWorkflow()->stages
            ->when($application->currentStage(), function ($stages) use ($application) {
                $currentPosition = $application->currentStage()->position;

                return $stages->filter(function ($stage) use ($currentPosition) {
                    return $stage->position < $currentPosition || $stage->position === $currentPosition + 1;
                });
            })
            ->sortBy('position')
            ->map(function ($stage) use ($application) {
                return [
                    'name' => $stage->position === $application->currentStage()->position + 1 ? 'Next Stage - ' . $stage->name : $stage->name,
                    'uuid' => $stage->uuid,
                ];
            });
    }

    public function setDateInToNow()
    {
        $this->dateIn = Carbon::now()->format('Y-m-d\TH:i:s');
        $this->dateOut = null;
    }

    public function setDateOutToNow()
    {
        $this->dateOut = Carbon::now()->format('Y-m-d\TH:i:s');
    }

    public function submit()
    {
        $this->validate();

        WorkflowHistory::create([
            'recommendation_uuid' => $this->application->currentRecommendation()->uuid,
            'from_stage_uuid' => $this->application->previousHistory() ? $this->application->previousHistory()->to_stage_uuid : null,
            'to_stage_uuid' => $this->toStageUuid,
            'date_in' => $this->dateIn,
            'date_out' => $this->dateOut,
            'reason' => $this->reason ?? null,
            'processed_by' => Auth::id(),
        ]);

        $this->application->current_workflow_stage_uuid = $this->toStageUuid;
        $this->application->save();

        $this->dispatch(
            'alert-modal',
            'Success',
            'History created successfully',
            'success'
        );
        
        $this->dispatch('refreshWorkflowHistory');
    }

    public function render()
    {
        return view('livewire.workflow.history.create');
    }
}
