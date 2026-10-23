<?php

namespace App\Livewire\Workflow\Stage;

use App\Models\Workflow;
use Livewire\Component;

class Index extends Component
{
    public Workflow $workflow;

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    public function render()
    {
        return view('livewire.workflow.stage.index', [
            'stages' => $this->workflow->stages()->with('permission')->orderBy('position')->get(),
        ]);
    }
}
