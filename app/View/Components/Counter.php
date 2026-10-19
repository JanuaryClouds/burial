<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Counter extends Component
{
    public string $label;

    public int $count = 0;

    public string $iconName;

    public int $iconPathsCount;

    public ?string $route = null;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $label,
        int $count,
        string $iconName,
        int $iconPathsCount,
        ?string $route = null
    ) {
        $this->label = $label;
        $this->count = $count;
        $this->iconName = $iconName;
        $this->iconPathsCount = $iconPathsCount;
        $this->route = $route;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.counter');
    }
}
