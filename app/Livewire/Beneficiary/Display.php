<?php

namespace App\Livewire\Beneficiary;

use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Models\Religion;
use App\Models\Sex;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Display extends Component
{
    public ?string $beneficiaryUuid = null;
    public ?Beneficiary $beneficiary = null;
    public ?Collection $draftBeneficiaries = null;
    public Collection $family;

    public function mount(?string $uuid = null, ?Collection $draftBeneficiaries): void
    {
        $this->draftBeneficiaries = $draftBeneficiaries;
        $this->beneficiaryUuid = $uuid;
    
        $this->queryBeneficiary();
    }

    #[On('beneficiary-selected')]
    public function handleBeneficiarySelected(string $uuid): void
    {
        $this->beneficiaryUuid = $uuid;

        $this->queryBeneficiary();
    }
    
    public function queryBeneficiary(): void
    {
        if ($this->beneficiaryUuid) {
            $this->beneficiary = $this->draftBeneficiaries->where('uuid', $this->beneficiaryUuid)
                ->first();

            $this->queryFamily($this->beneficiary);
        } else {
            $this->beneficiary = null;
        }
    }

    public function queryFamily(Beneficiary $beneficiary): void
    {
        $this->family = $beneficiary->family;
    }

    public function render()
    {
        return view('livewire.beneficiary.display');
    }
}
