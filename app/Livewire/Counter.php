<?php

namespace App\Livewire;

use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Locked;
use Livewire\Component;

class Counter extends Component
{
    public string $label;

    public int $count = 0;

    public string $iconName;

    public int $iconPathsCount;

    public string $route;

    public function mount(
        string $label,
        string $iconName,
        int $iconPathsCount,
        string $route
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
