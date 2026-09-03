<?php

namespace App\View\Components\Sidebar;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Routing\Route;
use Illuminate\View\Component;

class SubLink extends Component
{
    public string $route;

    public string $text;

    /**
     * Create a new component instance.
     */
    public function __construct(string $route, string $text)
    {
        $this->route = $route;
        $this->text = $text;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar.sub-link');
    }
}
