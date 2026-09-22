<?php

namespace App\Livewire\Beneficiary\Charts;

use App\Models\Beneficiary;
use Illuminate\Support\Collection;
use Livewire\Component;

class PerNatality extends Component
{
    public Collection $perNatality;

    public string $chartId = 'beneficiary-per-natality';

    public string $chartType = 'bar';

    public string $chartTitle = 'Perinatal and Neonatal Deaths';

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
                'count' => $this->perNatality->pluck('count'),
                'labels' => $this->perNatality->pluck('name'),
            ],
        ]);
    }

    public function getData()
    {
        $this->perNatality = Beneficiary::perNatality()
            ->get()
            ->map(function ($item) {
                return [
                    'name' => (string) $item->natality_group,
                    'count' => (int) $item->total,
                ];
            });
    }

    public function render()
    {
        return view('livewire.beneficiary.charts.per-natality');
    }
}
