<?php

namespace App\Livewire\Report;

use App\Models\Application;
use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Services\ApplicationService;
use Illuminate\Support\Collection;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Index extends Component
{
    #[Validate('required|date|before_or_equal:endDate')]
    public ?string $startDate = null;

    #[Validate('required|date|after_or_equal:startDate')]
    public ?string $endDate = null;

    public Collection $applications;

    public int $applicationsTotal = 0;

    public Collection $applicationsPerStatus;

    public int $beneficiariesTotal = 0;

    public int $beneficiariesPwd = 0;

    public Collection $beneficiariesNatality;

    public Collection $beneficiariesAgeGroups;

    public Collection $barangaysWithBeneficiaryCount;

    public function mount()
    {
        $this->startDate = now()->startOfYear()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->getData();

        dd(
            $this->beneficiariesTotal,
            $this->beneficiariesPwd,
            $this->beneficiariesNatality,
            $this->beneficiariesAgeGroups,
        );
    }

    public function filter()
    {
        $this->getData();
        
        $this->dispatch('refresh-chart', [
            'chartId' => 'application-per-status',
            'chartData' => [
                'count' => $this->applicationsPerStatus->pluck('count'),
                'labels' => $this->applicationsPerStatus->pluck('name'),
            ],
        ]);

        $this->dispatch('notification:alert', [
            'type' => 'success',
            'text' => 'Successfully filtered data'
        ]);

        // dd(
        //     $this->applications,
        //     $this->applicationsTotal,
        //     $this->applicationsPerStatus,
        //     $this->beneficiariesTotal,
        //     $this->beneficiariesPwd,
        //     $this->beneficiariesNatality,
        //     $this->beneficiariesAgeGroups,
        //     $this->barangaysWithBeneficiaryCount,
        // );
    }

    private function getData()
    {
        $this->indexApplications();
        $this->applicationsTotal = $this->applications->count();
        $this->getApplicationsPerStatus();
        $this->beneficiariesTotal = Beneficiary::index(null, null, null, $this->startDate, $this->endDate)->whereHas('application')->get()->count();
        $this->beneficiariesPwd = Beneficiary::onlyPwd($this->startDate, $this->endDate)->get()->count();
        $this->beneficiariesNatality = Beneficiary::perNatality($this->startDate, $this->endDate)->get();
        $this->getBeneficiaryAgeGroups();
        $this->getBarangays();
    }

    private function indexApplications()
    {
        $this->applications = Application::index(null, $this->startDate, $this->endDate, 'created_at', 'desc')
            ->get()
            ->map(function (Application $application) {
                return [
                    'tracking_number' => $application->tracking_no,
                    'client' => $application->client->fullname(),
                    'relationship_with_beneficiary' => $application->relationship->name,
                    'beneficiary' => $application->beneficiary->fullname(),
                    'age' => $application->beneficiary->age() . ' years old',
                    'PWD' => $application->beneficiary->pwd == 1 ? 'Yes' : 'No',
                    'status' => $application->currentStatus()['label'],
                ];
            });
    }

    private function getApplicationsPerStatus()
    {
        $this->applicationsPerStatus = Application::perStatus($this->startDate, $this->endDate)
            ->get()
            ->map(function (Application $application) {
                return [
                    'status' => $application->currentStatus()['label'],
                    'uuid' => $application->uuid,
                ];
            })
            ->groupBy('status')
            ->map(function ($collection, $key) {
                return [
                    'name' => ucfirst($key),
                    'count' => $collection->count(),
                ];
            })
            ->values();
    }

    private function getBeneficiaryAgeGroups()
    {
        $this->beneficiariesAgeGroups = Beneficiary::perAgeGroup($this->startDate, $this->endDate)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => (string) $item->age_group,
                    'count' => (int) $item->total,
                ];
            });;
    }

    private function getBarangays()
    {
        $this->barangaysWithBeneficiaryCount = Barangay::with([
                'beneficiary.application',
            ])
            ->when($this->startDate && $this->endDate, function ($query) {
                $query->whereHas('beneficiary.application', function ($query) {
                    $query->whereBetween('created_at', [$this->startDate, $this->endDate]);
                });
            })
            ->get()
            ->map(function (Barangay $barangay) {
                return [
                    'name' => ucfirst($barangay->name),
                    'count' => $barangay->beneficiary->count(),
                ];
            });
    }

    public function render()
    {
        return view('livewire.report.index');
    }
}
