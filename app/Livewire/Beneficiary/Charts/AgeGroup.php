<?php

namespace App\Livewire\Beneficiary\Charts;

use App\Models\Beneficiary;
use Illuminate\Support\Collection;
use Livewire\Component;

class AgeGroup extends Component
{
    public Collection $ageGroups;

    public string $chartId = 'beneficiary-age-group';

    public string $chartType = 'pie';

    public string $chartTitle = "Beneficiaries Per Age Group";

    public function mount()
    {
        $this->getAgeGroups();
    }
    
    public function refresh()
    {
        $this->getAgeGroups();
        $this->dispatch('refresh-chart', [
            'chartId' => $this->chartId,
            'chartData' => [
                'count' => $this->ageGroups->pluck('count'),
                'labels' => $this->ageGroups->pluck('name'),
            ],
        ]);
    }
    
    private function getAgeGroups(): void
    {
        $this->ageGroups = Beneficiary::ageGroup()
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
        return view('livewire.beneficiary.charts.age-group');
    }
}
