<?php

namespace App\Livewire\Beneficiary;

use App\Models\Beneficiary;
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
        $this->beneficiariesPerAgeGroup = $this->beneficiaries
            ->filter(fn (Beneficiary $beneficiary) => $beneficiary->application)
            ->map(function (Beneficiary $beneficiary) {
                return [
                    'uuid' => $beneficiary->uuid,
                    'age' => $beneficiary->age(),
                ];
            })
            ->groupBy(function ($beneficiary) {
                $age = $beneficiary['age'];

                if ($age <= 17) {
                    return '0 - 17';
                }

                if ($age >= 18 && $age <= 30) {
                    return '18 - 30';
                }

                if ($age >= 31 && $age <= 45) {
                    return '31 - 45';
                }

                if ($age >= 46 && $age <= 60) {
                    return '46 - 60';
                }

                return '61+';
            })
            ->map(function ($collection, $key) {
                return [
                    'name' => $key,
                    'count' => $collection->count(),
                ];
            })
            ->values();

        $this->dispatch('refresh-chart', [
            'chartId' => 'beneficiaries-per-age-group',
            'chartData' => [
                'labels' => $this->beneficiariesPerAgeGroup->pluck('name'),
                'count' => $this->beneficiariesPerAgeGroup->pluck('count')
            ]
        ]);

        $this->beneficiariesPerReligion = $this->beneficiaries
            ->filter(fn (Beneficiary $beneficiary) => $beneficiary->application)
            ->map(function (Beneficiary $beneficiary) {
                return [
                    'uuid' => $beneficiary->uuid,
                    'religion' => $beneficiary->religion->name
                ];
            })
            ->groupBy('religion')
            ->map(function ($collection, $key) {
                return [
                    'name' => $key,
                    'count' => $collection->count(),
                ];
            })
            ->values();

        $this->dispatch('refresh-chart', [
            'chartId' => 'beneficiaries-per-religion',
            'chartData' => [
                'labels' => $this->beneficiariesPerReligion->pluck('name'),
                'count' => $this->beneficiariesPerReligion->pluck('count')
            ]
        ]);

        $this->beneficiariesPerNatality = $this->beneficiaries
            ->filter(fn (Beneficiary $beneficiary) => $beneficiary->application)
            ->map(function (Beneficiary $beneficiary) {
                return [
                    'uuid' => $beneficiary->uuid,
                    'date_of_birth' => $beneficiary->date_of_birth,
                    'date_of_death' => $beneficiary->date_of_death,
                ];
            })
            ->filter(function ($beneficiary) {
                return Carbon::parse($beneficiary['date_of_death'])
                    ->diffInDays(Carbon::parse($beneficiary['date_of_birth'])) <= 28;
            })
            ->groupBy(function ($beneficiary) {
                $age = Carbon::parse($beneficiary['date_of_death'])->diffInDays(
                    Carbon::parse($beneficiary['date_of_birth'])
                );

                if ($age <= 7) {
                    return 'Perinatal';
                }

                if ($age >= 8 && $age <= 28) {
                    return 'Neonatal';
                }
            })
            ->map(function ($collection, $key) {
                return [
                    'name' => $key,
                    'count' => $collection->count(),
                ];
            })
            ->values();

        $this->dispatch('refresh-chart', [
            'chartId' => 'beneficiaries-per-natality',
            'chartData' => [
                'labels' => $this->beneficiariesPerNatality->pluck('name'),
                'count' => $this->beneficiariesPerNatality->pluck('count')
            ]
        ]);

        $this->beneficiariesPerMonth = $this->beneficiaries
            ->filter(fn (Beneficiary $beneficiary) => $beneficiary->application)
            ->map(function (Beneficiary $beneficiary) {
                return [
                    'uuid' => $beneficiary->uuid,
                    'registered_at' => $beneficiary->application->created_at,
                ];
            })
            ->groupBy(function ($beneficiary) {
                return Carbon::parse($beneficiary['registered_at'])->format('F Y');
            })
            ->map(function ($collection, $key) {
                return [
                    'name' => $key,
                    'count' => $collection->count(),
                ];
            })
            ->values();

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
