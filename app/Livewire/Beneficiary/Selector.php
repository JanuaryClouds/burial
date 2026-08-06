<?php

namespace App\Livewire\Beneficiary;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Selector extends Component
{
    public array $beneficiaries;

    public ?Beneficiary $selectedBeneficiary = null;

    public ?string $selectedBeneficiaryUuid = null;

    public function mount(): void
    {
        $this->draftBeneficiaries();
    }

    public function draftBeneficiaries(): void
    {
        $this->beneficiaries = Beneficiary::with([
            'sex',
            'religion',
            'barangay',
            'family',
        ])
            ->whereDoesntHave('application')
            ->where('created_by', Auth::user()->id)
            ->get()
            ->sortByDesc('created_at')
            ->mapWithKeys(function (Beneficiary $beneficiary) {
                $label = $beneficiary->fullname();
                $date = $beneficiary->created_at->format('M d, Y');

                return [$beneficiary->uuid => "{$label} (created {$date})"];
            })
            ->toArray();
    }

    public function updatedSelectedBeneficiaryUuid(?string $uuid): void
    {
        $this->selectedBeneficiary = $uuid
            ? Beneficiary::with(Beneficiary::relations())
                ->where('uuid', $uuid)
                ->first()
            : null;

        if ($this->selectedBeneficiary) {
            $this->dispatch('beneficiary-uuid-updated', uuid: $uuid);
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.selector');
    }
}
