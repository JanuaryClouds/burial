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

    public ?WorkflowHistory $selectedHistory = null;

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
        $application->loadMissing('client');
        $this->authorize('view', $application);
        $this->recommendations = $application
            ->recommendations()
            ->oldest()
            ->with([
                'funeralAssistanceType',
                'workflow',
                'remarks',
                'workflowHistory' => fn ($query) => $query->orderBy('date_in')->withCount('customValues'),
                'workflowHistory.toStage' => fn ($query) => $query->withCount('customFields'),
                'workflowHistory.fromStage',
                'workflowHistory.remarks.user',
            ])
            ->get();
    }

    public function showRemarks(?string $modelClass, ?string $uuid)
    {
        $this->dispatch('load-remarks', modelClass: null, id: null);

        $this->dispatch('load-remarks', modelClass: (string) 'App\\Models\\'.$modelClass, id: $uuid);
    }

    public function showHistoryDetails(string $uuid): void
    {
        $this->application->loadMissing('client');
        $this->authorize('view', $this->application);
        $history = WorkflowHistory::whereHas('recommendation', fn ($query) => $query->where('application_uuid', $this->application->uuid))->whereKey($uuid)->firstOrFail();
        $this->dispatch('load-details', historyUuid: $history->uuid);
    }

    public function clearSelectedHistory(): void
    {
        $this->dispatch('load-details', historyUuid: null);
    }

    public function render()
    {
        return view('livewire.workflow.history.index');
    }
}
