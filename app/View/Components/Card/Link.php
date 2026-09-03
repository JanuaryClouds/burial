<?php

namespace App\View\Components\Card;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    public string $route;
    public bool $active;
    public string $title;
    public string $description;
    public string $icon;
    public int $icon_paths;
    public string $classes;

    /**
     * Create a new component instance.
     */
    public function __construct(
        string $route = '#',
        bool $active = true,
        string $title = '',
        string $description = '',
        string $icon = '',
        int $icon_paths = 1,
        string $classes = '',
    ) {
        $this->route = $route;
        $this->active = $active;
        $this->title = $title;
        $this->description = $description;
        $this->icon = $icon;
        $this->icon_paths = $icon_paths;
        $this->classes = $classes;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.card.link');
    }
}
