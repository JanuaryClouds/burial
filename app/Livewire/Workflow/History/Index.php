<?php

namespace App\Livewire\Workflow\History;

use App\Models\Application;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    use HasPlaceholder;

    public Application $application;

    public Collection $recommendations;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->recommendations = $application->recommendations()->with(['workflowHistory', 'funeralAssistanceType'])->oldest()->get();
    }

    #[On('refreshWorkflowHistory')]
    public function refresh()
    {
        $this->recommendations = $this->application->recommendations()->with(['workflowHistory', 'funeralAssistanceType'])->oldest()->get();
    }

    public function render()
    {
        return view('livewire.workflow.history.index');
    }
}
