<?php

namespace App\Livewire\Client;

use App\Models\Client;
use App\Services\DataTableService;
use Illuminate\Support\Carbon;
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
                    'name' => $client->fullname(),
                    // 'application_tracking_no' => $client->application ? $client->application->tracking_no : 'DRAFT',
                    'address' => $client->address->barangay_name.', '.
                        ($client->address->municipality_name ?? $client->address->province_name).', '.
                        $client->address->region_name,
                    'recorded_at' => Carbon::parse($client->created_at)->diffForHumans(),
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
