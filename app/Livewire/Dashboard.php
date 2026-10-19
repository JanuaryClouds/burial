<?php

namespace App\Livewire;

use App\Models\Application;
use App\Services\DataTableService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Dashboard extends Component
{
    public Collection $applications;

    public Collection $mappedApplications;

    public Collection $columns;

    public string $dataTableId;

    public function mount()
    {
        $this->dataTableId = (string) Str::uuid();
        $this->loadData();
    }

    public function loadData()
    {
        if (Auth::user()->roles()->count() > 0) {
            $this->applications = Application::index()->get();
        } else {
            $this->applications = Application::index(Auth::id())->get();
            $this->mappedApplications = $this->applications
                ->map(function (Application $application) {
                    $client = $application->client;
                    $beneficiary = $application->beneficiary;
                    $status = $application->currentStatus();

                    return [
                        'uuid' => $application->uuid,
                        'tracking_no' => $application->tracking_no,
                        'client' => $client->fullname(),
                        'beneficiary' => $beneficiary->fullname(),
                        'application_date' => $application->created_at->format('F d, Y'),
                        'status' => $status,
                        'show_route' => route('application.show', $application),
                    ];
                });

            $this->columns = app(DataTableService::class)->getColumns($this->mappedApplications);

            $this->dispatch('refresh-dataTable', [
                'dataTableId' => $this->dataTableId,
                'rows' => $this->mappedApplications,
            ]);
        }
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
