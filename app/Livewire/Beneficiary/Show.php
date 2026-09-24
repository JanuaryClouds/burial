<?php

namespace App\Livewire\Beneficiary;

use App\Models\Beneficiary;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Show extends Component
{
    use HasPlaceholder;

    public ?Beneficiary $beneficiary = null;

    public ?string $uuid = null;

    public function mount(?Beneficiary $beneficiary = null, ?string $uuid = null)
    {
        if ($uuid) {
            $this->beneficiary = Beneficiary::where('uuid', $uuid)->firstOrFail();
        } else {
            $this->beneficiary = $beneficiary;
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.show');
    }
}
