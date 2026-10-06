<?php

namespace App\Livewire\Application\Charts;

use App\Models\Application;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Collection;
use Livewire\Component;

class PerStatus extends Component
{
    use HasPlaceholder;

    public Collection $perStatus;

    public string $chartId = 'application-per-status';

    public string $chartType = 'pie';

    public string $chartTitle = 'Applications Per Status';

    public ?string $startDate = null;

    public ?string $endDate = null;

    public function mount(?string $startDate = null, ?string $endDate = null)
    {
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
        $this->perStatus = Application::perStatus($this->startDate, $this->endDate)
            ->get()
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
        return view('livewire.application.charts.per-status');
    }
}
