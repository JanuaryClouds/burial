<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Services\ApplicationService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Timeline extends Component
{
    public Application $application;

    public Collection $workflowHistory;

    private ApplicationService $services;

    public function boot(ApplicationService $applicationService)
    {
        $this->services = $applicationService;
    }

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->workflowHistory = $application->workflowHistory()->with(['toStage', 'fromStage'])->orderBy('date_in')->get();
    }

    public function refresh()
    {
        $this->workflowHistory = $this->application->workflowHistory()->with(['toStage', 'fromStage'])->orderBy('date_in')->get();
    }

    public function render()
    {
        return view('livewire.application.timeline');
    }
}
