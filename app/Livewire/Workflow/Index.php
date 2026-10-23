<?php

namespace App\Livewire\Workflow;

use App\Models\Workflow;
use App\Services\DataTableService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public Collection $workflows;

    public Collection $mappedWorkflows;

    public Collection $columns;

    public string $dataTableId = 'workflow-dataTable';

    public function boot(): void
    {
        abort_unless(auth()->user()?->hasRole('superadmin'), 403);
    }

    public function mount()
    {
        $this->loadData();
    }

    #[On('workflow-created')]
    public function loadData()
    {
        $this->workflows = Workflow::index()
            ->get();

        $this->mappedWorkflows = $this->workflows
            ->map(function (Workflow $workflow) {
                return [
                    'uuid' => $workflow->uuid,
                    'name' => $workflow->name,
                    'version' => $workflow->version_number,
                    'status' => [
                        'label' => $workflow->is_active ? 'Active' : 'Inactive',
                        'badgeColor' => $workflow->is_active ? 'success' : 'secondary',
                    ],
                    'assigned_funeral_assistance_types' => $workflow->funeral_assistance_types_count,
                    'show_route' => route('workflows.edit', $workflow),
                ];
            });

        $this->columns = app(DataTableService::class)->getColumns($this->mappedWorkflows);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedWorkflows,
        ]);
    }

    public function render()
    {
        return view('livewire.workflow.index');
    }
}
