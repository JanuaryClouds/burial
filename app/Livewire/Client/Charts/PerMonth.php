<?php

namespace App\Livewire\Client\Charts;

use App\Models\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class PerMonth extends Component
{
    public Collection $perMonth;

    public string $chartId = 'client-per-month';

    public string $chartType = 'line';

    public string $chartTitle = 'Clients Per Month';

    public function mount()
    {
        $this->getData();
    }

    public function refresh()
    {
        $this->getData();
        $this->dispatch('refresh-chart', [
            'chartId' => $this->chartId,
            'chartData' => [
                'count' => $this->perMonth->pluck('count'),
                'labels' => $this->perMonth->pluck('name'),
            ],
        ]);
    }

    private function getData()
    {
        $this->perMonth = Client::perMonth()->get()
            ->map(function ($item) {
                return [
                    'name' => (string) Carbon::create($item->year, $item->month)->format('M Y'),
                    'count' => (int) $item->total,
                ];
            });
    }

    public function render()
    {
        return view('livewire.client.charts.per-month');
    }
}
