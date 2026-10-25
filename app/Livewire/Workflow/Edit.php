<?php

namespace App\Livewire\Workflow;

use App\Livewire\Workflow\Concerns\EditsWorkflowVersion;
use App\Models\Workflow;
use Livewire\Component;

class Edit extends Component
{
    use EditsWorkflowVersion;

    public function mount(Workflow $workflow): void
    {
        $this->initializeEditor($workflow);
    }
}
