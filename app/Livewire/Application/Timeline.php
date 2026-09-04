<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Timeline extends Component
{
    public Application $application;

    public Collection $recommendations;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->recommendations = $application->recommendations()->with('workflowHistory')->oldest()->get();
    }

    #[On('refreshWorkflowHistory')]
    public function refresh()
    {
        $this->recommendations = $this->application->recommendations()->with('workflowHistory')->oldest()->get();
    }

    public function render()
    {
        return view('livewire.application.timeline');
    }
}
