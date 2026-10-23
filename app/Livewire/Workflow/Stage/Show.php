<?php

namespace App\Livewire\Workflow\Stage;

use App\Models\WorkflowStage;
use Livewire\Component;

class Show extends Component
{
    public WorkflowStage $stage;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    public function render()
    {
        $this->stage->loadMissing('permission');

        return view('livewire.workflow.stage.show');
    }
}
