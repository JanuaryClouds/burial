<?php

namespace App\Livewire\Beneficiary\Charts;

use App\Models\Beneficiary;
use Illuminate\Support\Collection;
use Livewire\Component;

class PerAgeGroup extends Component
{
    public Collection $perAgeGroups;

    public string $chartId = 'beneficiary-per-age-group';

    public string $chartType = 'pie';

    public string $chartTitle = "Beneficiaries Per Age Group";

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
                'count' => $this->perAgeGroups->pluck('count'),
                'labels' => $this->perAgeGroups->pluck('name'),
            ],
        ]);
    }
    
    private function getData(): void
    {
        $this->perAgeGroups = Beneficiary::perAgeGroup()
            ->get()
            ->map(function ($item) {
                return [
                    'name' => (string) $item->age_group,
                    'count' => (int) $item->total,
                ];
            });
    }

    public function render()
    {
        return view('livewire.beneficiary.charts.per-age-group');
    }
}
