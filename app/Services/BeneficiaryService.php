<?php

namespace App\Services;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;

class BeneficiaryService
{
    public function index(?string $user_id = null, string $orderBy = 'created_at', string $orderDirection = 'asc')
    {
        return Beneficiary::index(
            $user_id,
            null,
            null,
            null,
            null,
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

    public function update(Beneficiary $beneficiary, array $data): Beneficiary
    {
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
