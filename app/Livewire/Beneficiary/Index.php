<?php

namespace App\Livewire\Beneficiary;

use App\Models\Beneficiary;
use App\Services\DataTableService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public Collection $beneficiaries;

    public Collection $mappedBeneficiaries;

    public Collection $columns;

    public string $dataTableId;

    public function mount(Collection $columns)
    {
        $this->dataTableId = (string) Str::uuid();
        $this->loadData();
    }

    public function loadData()
    {
        $this->beneficiaries = Beneficiary::index(
            Auth::user()->roles()->count() > 0 ? null : Auth::id()
        )
        ->orderBy('created_at', 'desc')
        ->get();

        $this->mappedBeneficiaries = $this->beneficiaries
            ->map(function (Beneficiary $beneficiary) {
                return [
                    'uuid' => $beneficiary->uuid,
                    'name' => $beneficiary->fullname(),
                    // 'application_tracking_no' => $beneficiary->application?->tracking_no ?? 'DRAFT',
                    'age' => Carbon::parse($beneficiary->date_of_birth)->format('Y').' - '.
                        Carbon::parse($beneficiary->date_of_death)->format('Y').
                        ' ('.$beneficiary->age().' y/o)',
                    'place_of_birth' => $beneficiary->address->barangay_name.', '.
                        ($beneficiary->address->province_name ?? $beneficiary->address->municipality_name).', '.
                        $beneficiary->address->region_name,
                    'recorded_at' => Carbon::parse($beneficiary->created_at)->diffForHumans(),
                    'show_route' => route('beneficiary.show', $beneficiary)
                ];
            });

        $this->columns = app(DataTableService::class)->getColumns($this->mappedBeneficiaries);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedBeneficiaries,
        ]);
    }

    public function render()
    {
        return view('livewire.beneficiary.index');
    }
}
