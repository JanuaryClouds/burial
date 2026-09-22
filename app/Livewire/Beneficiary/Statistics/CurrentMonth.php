<?php

namespace App\Livewire\Beneficiary\Statistics;

use App\Models\Beneficiary;
use Livewire\Component;

class CurrentMonth extends Component
{
    public int $count = 0;

    public string $iconName = 'profile-circle';

    public int $iconPathsCount = 3;

    public string $label = 'Total Beneficiaries this Month';

    public function mount()
    {
        $this->getCount();
    }

    public function getCount(): void
    {
        $this->count = Beneficiary::currentMonth()->get()?->count();
    }

    public function render()
    {
        return view('livewire.beneficiary.statistics.current-month');
    }
}
