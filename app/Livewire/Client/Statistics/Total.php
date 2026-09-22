<?php

namespace App\Livewire\Client\Statistics;

use App\Models\Client;
use Livewire\Component;

class Total extends Component
{
    public int $count = 0;

    public string $iconName = 'people';

    public int $iconPathsCount = 5;

    public string $label = 'Total Number of Clients';

    public function mount()
    {
        $this->getCount();
    }

    public function getCount()
    {
        $this->count = Client::total()->get()?->count();
    }

    public function render()
    {
        return view('livewire.client.statistics.total');
    }
}
