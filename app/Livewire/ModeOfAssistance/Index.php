<?php

namespace App\Livewire\ModeOfAssistance;

use App\Models\ModeOfAssistance;
use App\Services\DataTableService;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public Collection $modeOfAssistances;

    public Collection $mappedModeOfAssistances;

    public Collection $columns;

    public string $dataTableId = 'mode-of-assistances-dataTable';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->modeOfAssistances = ModeOfAssistance::index()
            ->get();

        $this->mappedModeOfAssistances = $this->modeOfAssistances
            ->map(function (ModeOfAssistance $modeOfAssistance) {
                return [
                    'id' => $modeOfAssistance->id,
                    'name' => $modeOfAssistance->name,
                    'show_route' => route('mode_of_assistances.edit', $modeOfAssistance->id),
                ];
            });

        $this->columns = app(DataTableService::class)
            ->getColumns($this->mappedModeOfAssistances, ['id']);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedModeOfAssistances,
        ]);
    }

    public function render()
    {
        return view('livewire.mode-of-assistance.index');
    }
}
