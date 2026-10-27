<?php

namespace App\Livewire\Workflow\History;

use App\Models\Application;
use App\Models\WorkflowHistory;
use App\Traits\Livewire\GuardsApplicationView;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public function boot(): void
    {
        abort_unless(auth()->user()?->isInternal(), 403);
    }

    use GuardsApplicationView;
    use HasPlaceholder;

    #[Locked]
    public ?WorkflowHistory $selectedHistory = null;

    #[Locked]
    public Application $application;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->authorizeApplication();
    }

    private function authorizeApplication(): void
    {
        $this->application->loadMissing('client');
        $this->authorize('view', $this->application);
    }

    #[On('load-details')]
    public function loadDetails(?string $historyUuid = null)
    {
        $this->authorizeApplication();
        if ($historyUuid) {
            $this->selectedHistory = WorkflowHistory::query()
                ->whereHas('recommendation', fn ($query) => $query->where('application_uuid', $this->application->uuid))
                ->when(! auth()->user()->hasOfficeAccess(), fn ($query) => $query->where('recommendation_uuid', $this->application->current_recommendation_uuid))
                ->whereKey($historyUuid)->firstOrFail();
        } else {
            $this->selectedHistory = null;
        }
    }

    public function render()
    {
        $this->authorizeApplication();
        if ($this->selectedHistory) {
            $this->selectedHistory = WorkflowHistory::with(['toStage.customFields', 'fromStage', 'processedBy', 'customValues.customField'])
                ->whereHas('recommendation', fn ($query) => $query->where('application_uuid', $this->application->uuid))
                ->when(! auth()->user()->hasOfficeAccess(), fn ($query) => $query->where('recommendation_uuid', $this->application->current_recommendation_uuid))
                ->whereKey($this->selectedHistory->uuid)->firstOrFail();
        }

        return view('livewire.workflow.history.show');
    }
}
