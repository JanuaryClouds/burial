<?php

namespace App\Livewire\Beneficiary\Statistics;

use App\Models\Beneficiary;
use Livewire\Component;

class PwdCount extends Component
{
    public string $label = "PWD Beneficiaries";

    public int $count = 0;

    public string $iconName = 'profile-user';

    public int $iconPathsCount = 4;

    public function mount()
    {
        $this->getCount();
    }

    public function getCount(): void
    {
        $this->count = Beneficiary::onlyPwd()->get()->count();
    }

    public function render()
    {
        return view('livewire.beneficiary.statistics.pwd-count');
    }
}
