<?php

namespace App\Livewire\Client;

use App\Models\Client;
use App\Services\DataTableService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public string $dataTableId;

    public Collection $columns;

    public Collection $clients;

    public Collection $mappedClients;

    public function mount()
    {
        $this->dataTableId = (string) Str::uuid();
        $this->loadData();
    }

    public function loadData()
    {
        $this->clients = Client::index(
            Auth::user()->roles()->count() > 0 ? null : Auth::id()
        )
            ->orderBy('created_at', 'desc')
            ->get();

        $this->mappedClients = $this->clients
            ->map(function (Client $client) {
                return [
                    'uuid' => $client->uuid,
                    'application_tracking_no' => $client->application ? $client->application->tracking_no : 'DRAFT',
                    'name' => $client->fullname(),
                    'beneficiary' => $client->application ? $client->application->beneficiary->fullname() : 'N/A',
                    'created_at' => $client->created_at->format('M d, Y'),
                    'show_route' => route('client.show', $client->uuid),
                ];
            });

        $this->columns = app(DataTableService::class)->getColumns($this->mappedClients);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedClients,
        ]);
    }

    public function render()
    {
        return view('livewire.client.index');
    }
}
