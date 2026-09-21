<?php

namespace App\Livewire\Beneficiary\Family;

use App\Models\BeneficiaryFamily;
use Livewire\Component;

class Show extends Component
{
    public BeneficiaryFamily $member;

    public function mount(BeneficiaryFamily $member)
    {
        $this->member = $member->loadMissing(['sex', 'civil', 'relationship']);
    }

    public function render()
    {
        return view('livewire.beneficiary.family.show');
    }
}
