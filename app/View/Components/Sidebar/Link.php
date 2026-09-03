<?php

namespace App\View\Components\Sidebar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Link extends Component
{
    public ?string $route;

    public ?string $activeLink;

    public string $icon;

    public int $iconPathsCount;

    public string $label;

    public string $description;

    /**
     * Create a new component instance.
     */
    public function __construct(
        ?string $route,
        ?string $activeLink,
        string $icon,
        int $iconPathsCount,
        string $label,
        string $description
    )
    {
        $this->route = $route;
        $this->activeLink = $activeLink;
        $this->icon = $icon;
        $this->iconPathsCount = $iconPathsCount;
        $this->label = $label;
        $this->description = $description;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar.link');
    }
}
