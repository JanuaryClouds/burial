<?php

namespace App\Livewire;

use App\Models\Application;
use App\Services\ApplicationService;
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

    public Collection $applicationsPerStatus;

    public Collection $applicationsPerRelationship;

    public array $applicationsPerStatusCount;

    public function mount()
    {
        $this->dataTableId = (string) Str::uuid();
        $this->loadData();
    }

    public function loadData()
    {
        if (Auth::user()->isInternal() && ! Auth::user()->hasOfficeAccess()) {
            $this->applications = $this->mappedApplications = $this->columns = collect();
            $this->applicationsPerStatus = $this->applicationsPerRelationship = collect();
            $this->applicationsPerStatusCount = [];

            return;
        }
        if (Auth::user()->hasOfficeAccess()) {
            $this->applications = Application::index()->get();

            $this->applicationsPerStatus = app(ApplicationService::class)
                ->groupByStatus($this->applications);

            $this->applicationsPerRelationship = app(ApplicationService::class)
                ->groupByRelationship($this->applications);

            $this->dispatch('refresh-chart', [
                'chartId' => 'applications-per-status',
                'chartData' => [
                    'count' => $this->applicationsPerStatus->pluck('count'),
                    'labels' => $this->applicationsPerStatus->pluck('name'),
                ],
            ]);

            $this->dispatch('refresh-chart', [
                'chartId' => 'applications-per-relationship',
                'chartData' => [
                    'count' => $this->applicationsPerRelationship->pluck('count'),
                    'labels' => $this->applicationsPerRelationship->pluck('name'),
                ],
            ]);
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

        $this->applicationsPerStatusCount = $this->applications
            ->countBy(fn (Application $application) => $application->currentStatus()['label'])
            ->toArray();
    }

    public function render()
    {
        $this->loadData();
        $isStaffDashboard = Auth::user()->hasOfficeAccess();

        return view('livewire.dashboard', [
            'isStaffDashboard' => $isStaffDashboard,
            'recentApplications' => $isStaffDashboard
                ? $this->applications->sortBy([
                    ['created_at', 'desc'],
                    ['uuid', 'desc'],
                ])->take(20)
                : collect(),
        ]);
    }
}
