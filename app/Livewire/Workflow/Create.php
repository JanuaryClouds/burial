<?php

namespace App\Livewire\Workflow;

use App\Livewire\Workflow\Concerns\EditsWorkflowVersion;
use Livewire\Component;

class Create extends Component
{
    use EditsWorkflowVersion;

    public function mount(): void
    {
        $this->initializeEditor();
    }
}
