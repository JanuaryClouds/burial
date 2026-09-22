<?php

namespace App\Livewire;

use Illuminate\Support\Collection;
use Livewire\Component;

class Chart extends Component
{
    public string $chartId;

    public Collection $chartData;

    public string $chartType;

    public ?string $chartTitle = null;

    public function mount(string $chartId, Collection $chartData, string $chartType, ?string $chartTitle)
    {
        $this->chartId = $chartId;
        $this->chartData = $chartData;
        $this->chartType = $chartType;
        $this->chartTitle = $chartTitle;
    }

    public function render()
    {
        return view('livewire.chart');
    }
}
