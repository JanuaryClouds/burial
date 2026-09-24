<?php

namespace App\Livewire;

use Livewire\Component;

class Counter extends Component
{
    public string $label;

    public int $count = 0;

    public string $iconName;

    public int $iconPathsCount;

    public ?string $route = null;

    public function mount(
        string $label,
        string $iconName,
        int $iconPathsCount,
        ?string $route = null
    ) {
        $this->label = $label;
        $this->iconName = $iconName;
        $this->iconPathsCount = $iconPathsCount;
        $this->route = $route;
    }

    public function render()
    {
        return view('livewire.counter');
    }
}
