<?php

namespace App\Livewire\Beneficiary\Family;

use App\Models\Beneficiary;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public Beneficiary $beneficiary;

    public array $family;

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;

        $this->family = $this->beneficiary->family->toArray();
    }

    #[On('refreshFamily')]
    public function refreshFamily()
    {
        $this->beneficiary->refresh();

        $this->family = $this->beneficiary->family->toArray();
    }

    public function addFamilyMember()
    {
        if (count($this->family) < 5) {
            $this->family[] = [
                'name' => '',
                'dateOfBirth' => '',
                'civilId' => '',
                'relationshipId' => '',
                'occupation' => '',
                'income' => '',
            ];
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.family.index');
    }
}
