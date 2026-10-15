<?php

namespace App\Livewire\Application\Statistics;

use App\Models\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Status extends Component
{
    public Collection $applications;

    public string $status;

    public int $count = 0;

    public string $iconName;

    public int $iconPathsCount;

    public ?string $route = null;

    public function mount(Collection $applications, string $status, string $iconName, int $iconPathsCount, ?string $route = null)
    {
        $this->applications = $applications;
        $this->status = $status;
        $this->getCount();
        $this->iconName = $iconName;
        $this->iconPathsCount = $iconPathsCount;
        $this->route = $route;
    }

    public function getCount(): void
    {
        $this->count = $this->applications
            ->map(function (Application $application) {
                return [
                    'uuid' => $application->uuid,
                    'status' => $application->currentStatus()['label'],
                ];
            })
            ->filter(function ($application) {
                return $application['status'] === $this->status;
            })
            ->count();
    }

    public function render()
    {
        return view('livewire.application.statistics.status');
    }
}
