<?php

namespace App\Livewire\Beneficiary\Charts;

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
        $this->getPwdCount();
    }

    public function refresh()
    {
        $this->getPwdCount();
    }

    private function getPwdCount()
    {
        $this->count = Beneficiary::pwdGroup()->get()->count();
    }

    public function render()
    {
        return view('livewire.beneficiary.charts.pwd-count');
    }
}
