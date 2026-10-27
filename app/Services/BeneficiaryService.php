<?php

namespace App\Services;

use App\Models\Beneficiary;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BeneficiaryService
{
    public function index(?string $user_id = null)
    {
        return Beneficiary::index(
            $user_id,
        )
            ->get()
            ->map(function (Beneficiary $beneficiary) {
                $application = $beneficiary->application?->load('workflowStage');
                $status = $application ? $application->currentStatus() : 'Draft';

                return [
                    'application_tracking_no' => $application ? $application->tracking_no : 'Draft',
                    'beneficiary' => $beneficiary->fullname(),
                    'date_of_birth' => $beneficiary->date_of_birth,
                    'date_of_death' => $beneficiary->date_of_death.' ('.$beneficiary->age().')',
                    'religion' => $beneficiary->religion?->name,
                    'status' => $status,
                    'show_route' => route('beneficiary.show', $beneficiary),
                ];
            });
    }

    /**
     * Summary of groupByAgeGroup
     */
    public function groupByAgeGroup(Collection $beneficiaries): Collection
    {
        return $beneficiaries
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
    }

    /**
     * Summary of groupByReligion
     */
    public function groupByReligion(Collection $beneficiaries): Collection
    {
        return $beneficiaries
            ->filter(fn (Beneficiary $beneficiary) => $beneficiary->application)
            ->map(function (Beneficiary $beneficiary) {
                return [
                    'uuid' => $beneficiary->uuid,
                    'religion' => $beneficiary->religion->name,
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
    }

    /**
     * Summary of groupByNatality
     */
    public function groupByNatality(Collection $beneficiaries): Collection
    {
        return $beneficiaries
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
    }

    /**
     * Summary of groupPerMonth
     */
    public function groupPerMonth(Collection $beneficiaries): Collection
    {
        return $beneficiaries
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
    }

    public function update(Beneficiary $beneficiary, array $data): Beneficiary
    {
        Gate::authorize('update', $beneficiary);
        $beneficiary->update([
            'first_name' => $data['firstName'],
            'middle_name' => $data['middleName'],
            'last_name' => $data['lastName'],
            'suffix' => $data['suffix'],
            'sex_id' => $data['sexId'],
            'religion_id' => $data['religionId'],
            'date_of_birth' => $data['dateOfBirth'],
            'date_of_death' => $data['dateOfDeath'],
            'pwd' => $data['pwd'] ?? false,
        ]);

        return $beneficiary->fresh();
    }

    /**
     * Summary of store
     */
    public function store(array $data): Beneficiary
    {
        Gate::authorize('create', Beneficiary::class);

        return Beneficiary::create([
            'first_name' => $data['firstName'],
            'middle_name' => $data['middleName'],
            'last_name' => $data['lastName'],
            'suffix' => $data['suffix'],
            'sex_id' => $data['sexId'],
            'religion_id' => $data['religionId'],
            'date_of_birth' => $data['dateOfBirth'],
            'date_of_death' => $data['dateOfDeath'],
            'pwd' => $data['pwd'] ?? false,
            'created_by' => Auth::id(),
        ]);
    }

    public function reportIndex($startDate, $endDate)
    {
        return Beneficiary::with([
            'client',
            'client.claimant',
            'client.funeralAssistance',
            'religion',
        ])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereHas('client', function ($q) {
                $q->orderBy('tracking_no', 'asc');
            })
            ->get()
            ->map(function ($beneficiary) {
                $assistance = 'Pending';
                $client = $beneficiary->client;

                if ($client?->claimant?->count() > 0) {
                    $assistance = 'Burial Assistance';
                }

                if ($client?->funeralAssistance?->count() > 0) {
                    $assistance = 'Libreng Libing';
                }

                if ($client?->referral?->count() > 0) {
                    $assistance = 'Referral';
                }

                return [
                    'client_tracking_no' => $beneficiary->client?->tracking_no,
                    'beneficiary' => $beneficiary->fullname(),
                    'date_of_birth' => $beneficiary->date_of_birth,
                    'date_of_death' => $beneficiary->date_of_death,
                    'religion' => $beneficiary->religion?->name,
                    'assistance' => $assistance,
                ];
            });
    }
}
