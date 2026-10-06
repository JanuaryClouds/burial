<?php

namespace App\Livewire\Workflow\History;

use App\Models\WorkflowHistory;
use Livewire\Component;

class Show extends Component
{
    public ?WorkflowHistory $selectedHistory = null;

    public function mount(?WorkflowHistory $selectedHistory = null)
    {
        $this->selectedHistory = $selectedHistory;
    }

    public function render()
    {
        return view('livewire.workflow.history.show');
    }
}
