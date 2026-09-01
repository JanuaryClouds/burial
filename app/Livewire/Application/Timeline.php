<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Timeline extends Component
{
    public Application $application;

    public Collection $recommendations;

    private ApplicationService $services;

    public function boot(ApplicationService $applicationService)
    {
        $this->services = $applicationService;
    }

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->recommendations = $application->recommendations()->with('workflowHistory')->oldest()->get();
    }

    public function refresh()
    {
        $this->recommendations = $this->application->recommendations()->with('workflowHistory')->oldest()->get();
    }

    public function render()
    {
        return view('livewire.application.timeline');
    }
}
