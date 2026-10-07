<?php

namespace App\Livewire\Workflow\History;

use App\Models\WorkflowHistory;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    use HasPlaceholder;

    public ?WorkflowHistory $selectedHistory = null;

    public function mount(?WorkflowHistory $selectedHistory = null)
    {
        $this->selectedHistory = $selectedHistory;
    }

    #[On('load-details')]
    public function loadDetails(?string $historyUuid = null)
    {
        if ($historyUuid) {
            $this->selectedHistory = WorkflowHistory::with([
                    'remarks',
                    'toStage',
                    'fromStage',
                    'remarks.user',
                ])
                ->firstWhere('uuid', $historyUuid);

            $this->dispatch(
                'load-remarks',
                modelClass: get_class($this->selectedHistory),
                id: $this->selectedHistory->uuid
            );
        } else {
            $this->selectedHistory = null;
            $this->dispatch(
                'load-remarks',
                modelClass: null,
                id: null
            );
        }
    }

    public function render()
    {
        return view('livewire.workflow.history.show');
    }
}
