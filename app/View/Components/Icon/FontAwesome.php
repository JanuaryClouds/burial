<?php

namespace App\View\Components\Icon;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class FontAwesome extends Component
{
    public string $icon;
    public ?string $size;
    public ?string $color;

    /**
     * Create a new component instance.
     */
    public function __construct(string $icon, ?string $size, ?string $color)
    {
        $this->icon = $icon;
        $this->size = $size;
        $this->color = $color;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.icon.font-awesome');
    }
}
