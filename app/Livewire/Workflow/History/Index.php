<?php

namespace App\Livewire\Workflow\History;

use App\Models\Application;
use App\Models\WorkflowHistory;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use HasPlaceholder;

    public Application $application;

    public Collection $recommendations;

    public WorkflowHistory $selectedHistory;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->loadTimeline($application);
    }

    #[On('refreshWorkflowHistory')]
    public function refresh()
    {
        $this->loadTimeline($this->application);
    }

    public function loadTimeline(Application $application)
    {
        $this->recommendations = $application
            ->recommendations()
            ->oldest()
            ->with([
                'remarks',
                'workflowHistory.toStage',
                'workflowHistory.fromStage',
                'workflowHistory.remarks.user',
                'funeralAssistanceType',
            ])
            ->get();
    }

    public function showRemarks(?string $modelClass, ?string $uuid)
    {
        $this->dispatch('load-remarks', modelClass: null, id: null);

        $this->dispatch('load-remarks', modelClass: (string) 'App\\Models\\'.$modelClass, id: $uuid);
    }

    // public function showHistoryDetails(string $uuid)
    // {
    //     $historyUuid = WorkflowHistory::firstWhere('uuid', $uuid)?->uuid;

    //     $this->dispatch('load-details', historyUuid: $historyUuid);
    // }

    // public function clearSelectedHistory()
    // {
    //     $this->dispatch('load-details', historyUuid: null);
    // }

    public function render()
    {
        return view('livewire.workflow.history.index');
    }
}
