<?php

namespace App\Livewire\Client\Statistics;

use App\Models\Client;
use Livewire\Component;

class CurrentMonth extends Component
{
    public int $count = 0;

    public string $iconName = 'calendar';

    public int $iconPathsCount = 2;

    public string $label = 'Total Number of Clients This Month';

    public function mount()
    {
        $this->getCount();
    }

    public function getCount()
    {
        $this->count = Client::currentMonth()->get()?->count();
    }

    public function render()
    {
        return view('livewire.client.statistics.current-month');
    }
}
