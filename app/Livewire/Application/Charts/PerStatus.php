<?php

namespace App\Livewire\Application\Charts;

use App\Models\Application;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PerStatus extends Component
{
    use HasPlaceholder;

    public Collection $applications;

    public Collection $perStatus;

    public string $chartId = 'application-per-status';

    public string $chartType = 'pie';

    public string $chartTitle = 'Applications Per Status';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(Collection $applications, ?string $startDate = null, ?string $endDate = null)
    {
        $this->applications = $applications;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->getData();
    }

    public function refresh()
    {
        $this->getData();
        $this->dispatch('refresh-chart', [
            'chartId' => $this->chartId,
            'chartData' => [
                'count' => $this->perStatus->pluck('count'),
                'labels' => $this->perStatus->pluck('name'),
            ],
        ]);
    }

    private function getData(): void
    {
        $this->perStatus = $this->applications
            ->map(function (Application $application) {
                return [
                    'status' => $application->currentStatus()['label'],
                    'uuid' => $application->uuid,
                ];
            })
            ->groupBy('status')
            ->map(function ($collection, $key) {
                return [
                    'name' => ucfirst($key),
                    'count' => $collection->count(),
                ];
            })
            ->values();
    }

    public function render()
    {
        $this->refresh();

        return view('livewire.application.charts.per-status');
    }
}
