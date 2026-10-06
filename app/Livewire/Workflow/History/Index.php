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
            ->with([
                'remarks',
                'workflowHistory.toStage',
                'workflowHistory.fromStage',
                'workflowHistory.remarks.user',
                'funeralAssistanceType',
            ])
            ->oldest()
            ->get();
    }

    public function showHistoryDetails(string $uuid)
    {
        $history = WorkflowHistory::with('remarks.user')
            ->where('uuid', $uuid)
            ->firstOrFail();

        $this->dispatch('load-remarks', model: $history);
    }

    public function render()
    {
        return view('livewire.workflow.history.index');
    }
}
