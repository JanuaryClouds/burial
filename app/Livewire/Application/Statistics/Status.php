<?php

namespace App\Livewire\Application\Statistics;

use App\Models\Application;
use Illuminate\Support\Collection;
use Livewire\Component;

class Status extends Component
{
    public string $status;

    public int $count = 0;

    public string $iconName;

    public int $iconPathsCount;

    public ?string $route = null;

    public function mount(string $status, int $count, string $iconName, int $iconPathsCount, ?string $route = null)
    {
        $this->status = $status;
        $this->count = $count;
        $this->iconName = $iconName;
        $this->iconPathsCount = $iconPathsCount;
        $this->route = $route;
    }

    public function render()
    {
        return view('livewire.application.statistics.status');
    }
}
