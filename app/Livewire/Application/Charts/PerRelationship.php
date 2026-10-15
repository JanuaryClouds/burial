<?php

namespace App\Livewire\Application\Charts;

use App\Models\Application;
use Illuminate\Support\Collection;
use Livewire\Component;

class PerRelationship extends Component
{
    public Collection $applications;

    public Collection $perRelationship;

    public string $chartId = 'application-per-relationship';

    public string $chartTitle = 'Applications Per Relationship';

    public string $chartType = 'bar';

    public function mount(Collection $applications)
    {
        $this->applications = $applications;
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
        $this->perRelationship = $this->applications
            ->groupBy('relationship_id')
            ->map(function ($relationship) {
                return [
                    'name' => (string) $relationship->first()->relationship->name,
                    'count' => (int) $relationship->count(),
                ];
            });
    }

    public function render()
    {
        $this->refresh();

        return view('livewire.application.charts.per-relationship');
    }
}
