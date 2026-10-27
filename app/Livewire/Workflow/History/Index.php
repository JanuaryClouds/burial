<?php

namespace App\Livewire\Workflow\History;

use App\Models\Application;
use App\Models\WorkflowHistory;
use App\Traits\Livewire\GuardsApplicationView;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public function boot(): void
    {
        abort_unless(auth()->user()?->isInternal(), 403);
    }

    use GuardsApplicationView;
    use HasPlaceholder;

    public Application $application;

    public ?WorkflowHistory $selectedHistory = null;

    public function mount(Application $application)
    {
        $this->application = $application;
    }

    #[On('refreshWorkflowHistory')]
    public function refresh()
    {
        unset($this->recommendations);
    }

    #[Computed]
    public function recommendations(): Collection
    {
        $this->application->loadMissing(['client', 'referral', 'cancellation', 'rejection']);
        $this->authorize('view', $this->application);

        return $this->application
            ->recommendations()
            ->when(! auth()->user()->hasOfficeAccess(), fn ($query) => $query->whereKey($this->application->current_recommendation_uuid))
            ->oldest()
            ->with([
                'claimantAssignment',
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
        $this->authorize('view', $this->application);
        $this->dispatch('load-remarks', modelClass: null, id: null);

        $this->dispatch('load-remarks', modelClass: (string) 'App\\Models\\'.$modelClass, id: $uuid);
    }

    public function showHistoryDetails(string $uuid): void
    {
        $this->application->loadMissing('client');
        $this->authorize('view', $this->application);
        $history = WorkflowHistory::whereHas('recommendation', fn ($query) => $query->where('application_uuid', $this->application->uuid)
            ->when(! auth()->user()->hasOfficeAccess(), fn ($q) => $q->whereKey($this->application->current_recommendation_uuid)))->whereKey($uuid)->firstOrFail();
        $this->dispatch('load-details', historyUuid: $history->uuid);
    }

    public function clearSelectedHistory(): void
    {
        $this->dispatch('load-details', historyUuid: null);
    }

    public function render()
    {
        return view('livewire.workflow.history.index', ['recommendations' => $this->recommendations]);
    }
}
