<?php

namespace App\Livewire\Beneficiary\Charts;

use App\Models\Beneficiary;
use Illuminate\Support\Collection;
use Livewire\Component;

class NatalityGroup extends Component
{
    public Collection $natalityGroups;

    public string $chartId = 'beneficiary-natality-group';

    public string $chartType = 'bar';

    public string $chartTitle = 'Perinatal and Neonatal Deaths';

    public function mount()
    {
        $this->getNatalityGroups();
    }

    public function refresh()
    {
        $this->getNatalityGroups();
        $this->dispatch('refresh-chart', [
            'chartId' => $this->chartId,
            'chartData' => [
                'count' => $this->natalityGroups->pluck('count'),
                'labels' => $this->natalityGroups->pluck('name'),
            ],
        ]);
    }

    public function getNatalityGroups()
    {
        $this->natalityGroups = Beneficiary::natalityGroup()
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
        return view('livewire.beneficiary.charts.natality-group');
    }
}
