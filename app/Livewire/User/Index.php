<?php

namespace App\Livewire\User;

use App\Models\User;
use App\Services\DataTableService;
use Illuminate\Support\Collection;
use Livewire\Component;

class Index extends Component
{
    public Collection $users;

    public Collection $mappedUsers;

    public Collection $columns;

    public string $dataTableId = 'users-dataTable';

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->users = User::index()
            ->get();

        $this->mappedUsers = $this->users
            ->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->fullname(),
                    'role' => $user->roles()->count() > 0 ? 'Staff' : 'Client',
                    'active' => $user->is_active,
                    'email' => $user->email,
                    'contact_number' => $user->contact_number,
                    'show_route' => route('user.edit', $user),
                ];
            });

        $this->columns = app(DataTableService::class)
            ->getColumns($this->mappedUsers, ['id']);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedUsers,
        ]);
    }

    public function render()
    {
        return view('livewire.user.index');
    }
}
