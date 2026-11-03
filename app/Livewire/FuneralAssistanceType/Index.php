<?php

namespace App\Livewire\FuneralAssistanceType;

use App\Models\FuneralAssistanceType;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public Collection $funeralAssistanceTypes;

    public Collection $mappedFuneralAssistanceTypes;

    public Collection $columns;

    public string $dataTableId = 'funeral-assistance-types-dataTable';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->funeralAssistanceTypes = FuneralAssistanceType::index()
            ->get();

        $this->mappedFuneralAssistanceTypes = $this->funeralAssistanceTypes
            ->map(function (FuneralAssistanceType $funeralAssistanceType) {
                return [
                    'uuid' => $funeralAssistanceType->uuid,
                    'name' => $funeralAssistanceType->name,
                    'description' => $funeralAssistanceType->description,
                    'workflow' => $funeralAssistanceType->workflow->name ?? null,
                    'show_route' => route('funeral_assistance_types.edit', $funeralAssistanceType->uuid),
                ];
            });

        $this->columns = collect([
            ['data' => 'name'],
            ['data' => 'description'],
            ['data' => 'workflow'],
            ['data' => 'show_route'],
        ]);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedFuneralAssistanceTypes,
        ]);
    }

    public function render()
    {
        return view('livewire.funeral-assistance-type.index');
    }
}
