<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Services\ApplicationService;
use Livewire\Component;

class StatusTimeline extends Component
{
    public Application $application;

    public array $status;

    private ApplicationService $services;

    public function boot(ApplicationService $applicationService)
    {
        $this->services = $applicationService;
    }

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->status = $application->status();
    }

    public function refresh()
    {
        $this->status = $this->application->status();
    }

    public function render()
    {
        return view('livewire.application.status-timeline');
    }
}
