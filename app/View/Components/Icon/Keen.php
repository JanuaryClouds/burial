<?php

namespace App\View\Components\Icon;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Keen extends Component
{
    public string $icon;

    public ?string $size;

    public int $pathsCount;

    /**
     * Create a new component instance.
     */
    public function __construct(string $icon, ?string $size, int $pathsCount = 2)
    {
        $this->icon = $icon;
        $this->size = $size;
        $this->pathsCount = $pathsCount;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.icon.keen');
    }
}
