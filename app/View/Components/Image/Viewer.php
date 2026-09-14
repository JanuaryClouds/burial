<?php

namespace App\View\Components\Image;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Viewer extends Component
{
    public string $src;

    public string $alt;

    /**
     * Create a new component instance.
     */
    public function __construct(string $src, string $alt)
    {
        $this->src = $src;
        $this->alt = $alt;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.image.viewer');
    }
}
