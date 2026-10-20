<?php

namespace App\Livewire\Beneficiary;

use App\Models\Beneficiary;
use App\Services\BeneficiaryService;
use App\Services\DataTableService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class Index extends Component
{
    public Collection $beneficiaries;

    public Collection $mappedBeneficiaries;

    public Collection $columns;

    public string $dataTableId;

    public int $currentMonthBeneficiariesCount;

    public int $pwdBeneficiariesCount;

    public Collection $beneficiariesPerAgeGroup;

    public Collection $beneficiariesPerReligion;

    public Collection $beneficiariesPerNatality;

    public Collection $beneficiariesPerMonth;

    public function mount()
    {
        $this->dataTableId = (string) Str::uuid();
        $this->loadData();
    }

    public function loadData()
    {
        $this->beneficiaries = Beneficiary::index(
            Auth::user()->roles()->count() > 0 ? null : Auth::id()
        )
        ->orderBy('created_at', 'desc')
        ->get();

        $this->mappedBeneficiaries = $this->beneficiaries
            ->map(function (Beneficiary $beneficiary) {
                return [
                    'uuid' => $beneficiary->uuid,
                    'name' => $beneficiary->fullname(),
                    // 'application_tracking_no' => $beneficiary->application?->tracking_no ?? 'DRAFT',
                    'age' => Carbon::parse($beneficiary->date_of_birth)->format('Y').' - '.
                        Carbon::parse($beneficiary->date_of_death)->format('Y').
                        ' ('.$beneficiary->age().' y/o)',
                    'place_of_birth' => $beneficiary->address->barangay_name.', '.
                        ($beneficiary->address->province_name ?? $beneficiary->address->municipality_name).', '.
                        $beneficiary->address->region_name,
                    'application' => $beneficiary->application?->tracking_no ?? 'DRAFT',
                    'recorded' => Carbon::parse($beneficiary->created_at)->diffForHumans(),
                    'show_route' => route('beneficiary.show', $beneficiary)
                ];
            });

        $this->columns = app(DataTableService::class)->getColumns($this->mappedBeneficiaries);

        $this->dispatch('refresh-dataTable', [
            'dataTableId' => $this->dataTableId,
            'rows' => $this->mappedBeneficiaries,
        ]);

        $this->loadStatisticsData();
        $this->loadChartData();
    }

    private function loadStatisticsData(): void
    {
        $this->currentMonthBeneficiariesCount = $this->beneficiaries
            ->filter(fn (Beneficiary $beneficiary) => $beneficiary->application)
            ->filter(fn (Beneficiary $beneficiary) => Carbon::parse($beneficiary->application->created_at)->isSameMonth(now()))
            ->count();

        $this->pwdBeneficiariesCount = $this->beneficiaries
            ->filter(fn (Beneficiary $beneficiary) => $beneficiary->pwd)
            ->count();
    }

    private function loadChartData(): void
    {
        $this->beneficiariesPerAgeGroup = app(BeneficiaryService::class)
            ->groupByAgeGroup($this->beneficiaries);

        $this->dispatch('refresh-chart', [
            'chartId' => 'beneficiaries-per-age-group',
            'chartData' => [
                'labels' => $this->beneficiariesPerAgeGroup->pluck('name'),
                'count' => $this->beneficiariesPerAgeGroup->pluck('count')
            ]
        ]);

        $this->beneficiariesPerReligion = app(BeneficiaryService::class)
            ->groupByReligion($this->beneficiaries);

        $this->dispatch('refresh-chart', [
            'chartId' => 'beneficiaries-per-religion',
            'chartData' => [
                'labels' => $this->beneficiariesPerReligion->pluck('name'),
                'count' => $this->beneficiariesPerReligion->pluck('count')
            ]
        ]);

        $this->beneficiariesPerNatality = app(BeneficiaryService::class)
            ->groupByNatality($this->beneficiaries);

        $this->dispatch('refresh-chart', [
            'chartId' => 'beneficiaries-per-natality',
            'chartData' => [
                'labels' => $this->beneficiariesPerNatality->pluck('name'),
                'count' => $this->beneficiariesPerNatality->pluck('count')
            ]
        ]);

        $this->beneficiariesPerMonth = app(BeneficiaryService::class)
            ->groupPerMonth($this->beneficiaries);

        $this->dispatch('refresh-chart', [
            'chartId' => 'beneficiaries-per-month',
            'chartData' => [
                'labels' => $this->beneficiariesPerMonth->pluck('name'),
                'count' => $this->beneficiariesPerMonth->pluck('count')
            ]
        ]);
    }

    public function render()
    {
        return view('livewire.beneficiary.index');
    }
}
