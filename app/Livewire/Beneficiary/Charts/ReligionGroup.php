<?php

namespace App\Livewire\Beneficiary\Charts;

use App\Models\Beneficiary;
use Illuminate\Support\Collection;
use Livewire\Component;

class ReligionGroup extends Component
{
    public Collection $religionGroups;

    public string $chartId = 'beneficiary-religion-group';

    public string $chartType = 'pie';

    public string $chartTitle = "Beneficiaries Per Religion";

    public function mount()
    {
        $this->getReligionGroups();
    }

    public function refresh()
    {
        $this->getReligionGroups();
        $this->dispatch('refresh-chart', [
            'chartId' => $this->chartId,
            'chartData' => [
                'count' => $this->religionGroups->pluck('count'),
                'labels' => $this->religionGroups->pluck('name'),
            ],
        ]);
    }
    
    private function getReligionGroups(): void
    {
        $this->religionGroups = Beneficiary::religionGroup()
            ->get()
            ->map(function ($item) {
                return [
                    'name' => (string) $item->religion->name,
                    'count' => (int) $item->total,
                ];
            });
    }

    public function render()
    {
        return view('livewire.beneficiary.charts.religion-group');
    }
}
