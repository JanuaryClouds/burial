<?php

namespace App\Livewire\Client;

use App\Models\Client;
use App\Services\ClientService;
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

    public int $totalClientsCount;

    public int $currentMonthClientsCount;

    public Collection $clientsPerMonth;

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
                    'address' => $client->address->barangay_name.', '.
                        ($client->address->municipality_name ?? $client->address->province_name).', '.
                        $client->address->region_name,
                    'recorded_at' => Carbon::parse($client->created_at)->diffForHumans(),
                    'application_tracking_no' => $client->application ? $client->application->tracking_no : 'DRAFT',
                    'show_route' => route('client.show', $client->uuid),
                ];
            });

        $this->columns = app(DataTableService::class)->getColumns($this->mappedClients);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedClients,
        ]);

        $this->totalClientsCount = $this->clients
            ->filter(fn (Client $client) => $client->application)
            ->count();

        $this->currentMonthClientsCount = $this->clients
            ->filter(fn (Client $client) => $client->application)
            ->filter(fn (Client $client) => Carbon::parse($client->created_at)->isSameMonth(now()))
            ->count();

        $this->clientsPerMonth = app(ClientService::class)->groupByMonth($this->clients);

        $this->dispatch('refresh-chart', [
            'chartId' => 'client-per-month',
            'chartData' => [
                'labels' => $this->clientsPerMonth->pluck('name'),
                'count' => $this->clientsPerMonth->pluck('count'),
            ],
        ]);
    }

    public function render()
    {
        return view('livewire.client.index');
    }
}
