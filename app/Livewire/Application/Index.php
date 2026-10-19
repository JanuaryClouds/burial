<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Services\DataTableService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public Collection $columns;

    public Collection $applications;

    public Collection $mappedApplications;

    public string $dataTableId;

    public array $applicationsPerStatus;

    public function mount()
    {
        $this->dataTableId = (string) Str::uuid();
        $this->loadData();
    }

    public function loadData()
    {
        $this->applications = Application::index(
            Auth::user()->roles->isNotEmpty() ? null : Auth::user()->id
        )
            ->orderBy('tracking_no', 'desc')
            ->get();
        
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

        $this->applicationsPerStatus = $this->applications
            ->countBy(fn (Application $application) => $application->currentStatus()['label'])
            ->toArray();

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedApplications,
        ]);

        $this->columns = app(DataTableService::class)->getColumns($this->mappedApplications);
    }

    public function render()
    {
        return view('livewire.application.index');
    }
}
