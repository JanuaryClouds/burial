<?php

namespace App\Livewire\Application\Statistics;

use App\Models\Application;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Status extends Component
{
    public string $status;

    public int $count = 0;

    public string $iconName;

    public int $iconPathsCount;

    public ?string $route = null;

    public function mount(string $status, string $iconName, int $iconPathsCount, ?string $route = null)
    {
        $this->status = $status;
        $this->getCount();
        $this->iconName = $iconName;
        $this->iconPathsCount = $iconPathsCount;
        $this->route = $route;
    }

    public function getCount(): void
    {
        $this->count = Application::with([
            'client.interviews',
            'referral',
            'workflowStage',
            'assessment',
            'recommendations'
        ])
            ->when(Auth::user()->roles()->count() == 0, function ($query) {
                $query->whereHas('client', function ($query) {
                    $query->where('user_id', Auth::id());
                });
            })
            ->get()
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
