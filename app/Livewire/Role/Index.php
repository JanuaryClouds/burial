<?php

namespace App\Livewire\Role;

use App\Services\DataTableService;
use Illuminate\Support\Collection;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    public function boot(): void
    {
        abort_unless(auth()->user()?->isSystemAdministrator(), 403);
    }

    public Collection $roles;

    public Collection $mappedRoles;

    public Collection $columns;

    public string $dataTableId = 'roles-dataTable';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->roles = Role::select('id', 'name')
            ->whereNotIn('name', ['superadmin', 'staff', 'CSWDO'])
            ->get();

        $this->mappedRoles = $this->roles
            ->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'show_route' => route('role.edit', $role->id),
                ];
            });

        $this->columns = app(DataTableService::class)
            ->getColumns($this->mappedRoles, ['id']);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedRoles,
        ]);
    }

    public function render()
    {
        return view('livewire.role.index');
    }
}
