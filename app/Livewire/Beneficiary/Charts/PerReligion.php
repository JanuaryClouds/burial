<?php

namespace App\Livewire\Beneficiary\Charts;

use App\Models\Beneficiary;
use Illuminate\Support\Collection;
use Livewire\Component;

class PerReligion extends Component
{
    public Collection $perReligion;

    public string $chartId = 'beneficiary-per-religion';

    public string $chartType = 'pie';

    public string $chartTitle = "Beneficiaries Per Religion";

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
                'count' => $this->perReligion->pluck('count'),
                'labels' => $this->perReligion->pluck('name'),
            ],
        ]);
    }
    
    private function getData(): void
    {
        $this->perReligion = Beneficiary::perReligion()
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
        return view('livewire.beneficiary.charts.per-religion');
    }
}
