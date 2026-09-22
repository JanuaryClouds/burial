<?php

namespace App\Livewire\Beneficiary\Charts;

use App\Models\Beneficiary;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Livewire\Component;

class PerMonth extends Component
{
    public Collection $perMonth;

    public string $chartId = 'beneficiary-per-month';

    public string $chartType = 'line';

    public string $chartTitle = "Beneficiary Per Month";

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
        $this->perMonth = Beneficiary::perMonth()
            ->get()
            ->map(function ($item) {
                return [
                    'name' => (string) Carbon::create($item->year, $item->month)->format('M Y'),
                    'count' => (int) $item->total,
                ];
            });
    }

    public function render()
    {
        return view('livewire.beneficiary.charts.per-month');
    }
}
