<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Chart extends Component
{
    public string $chartId;

    public Collection $chartData;

    public string $chartType;

    public ?string $chartTitle = null;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $chartId,
        Collection $chartData,
        string $chartType,
        ?string $chartTitle = null
    ) {
        $this->chartId = $chartId;
        $this->chartData = $chartData;
        $this->chartType = $chartType;
        $this->chartTitle = $chartTitle;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.chart');
    }
}
