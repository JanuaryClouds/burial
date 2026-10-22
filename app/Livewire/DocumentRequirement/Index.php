<?php

namespace App\Livewire\DocumentRequirement;

use App\Models\DocumentRequirement;
use App\Services\DataTableService;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public Collection $documents;

    public Collection $mappedDocuments;

    public Collection $columns;

    public string $dataTableId = 'document-requirements-dataTable';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->documents = DocumentRequirement::index()
            ->get();

        $this->mappedDocuments = $this->documents
            ->map(function (DocumentRequirement $documentRequirement) {
                return [
                    'id' => $documentRequirement->id,
                    'name' => $documentRequirement->name,
                    'source' => $documentRequirement->source,
                    'updated_at' => $documentRequirement->updated_at->diffForHumans(),
                    'show_route' => route('document_requirements.edit', $documentRequirement)
                ];
            });

        $this->columns = app(DataTableService::class)->getColumns($this->mappedDocuments, ['id']);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedDocuments,
        ]);
    }

    public function render()
    {
        return view('livewire.document-requirement.index');
    }
}
