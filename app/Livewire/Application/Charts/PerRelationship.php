<?php

namespace App\Livewire\Application\Charts;

use App\Models\Application;
use Illuminate\Support\Collection;
use Livewire\Component;

class PerRelationship extends Component
{
    public Collection $perRelationship;

    public string $chartId = 'application-per-relationship';

    public string $chartTitle = 'Applications Per Relationship';

    public string $chartType = 'bar';

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
                'count' => $this->perRelationship->pluck('count'),
                'labels' => $this->perRelationship->pluck('name'),
            ],
        ]);
    }

    private function getData()
    {
        $this->perRelationship = Application::perRelationship()
            ->get()
            ->map(function ($item) {
                return [
                    'name' => (string) $item->relationship->name,
                    'count' => (int) $item->total,
                ];
            });
    }

    public function render()
    {
        return view('livewire.application.charts.per-relationship');
    }
}
