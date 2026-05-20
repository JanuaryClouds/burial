<?php

namespace App\Services;

use App\Models\Beneficiary;

class BeneficiaryService
{
    public function index(?int $user_id = null)
    {
        return Beneficiary::with([
            'client',
            'client.claimant',
            'client.funeralAssistance',
            'religion',
        ])
            ->when($user_id, function ($query) use ($user_id) {
                $query->whereHas('client', function ($q) use ($user_id) {
                    $q->where('user_id', $user_id);
                });
            })
            ->orderBy('created_at', 'desc')
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
                    'show_route' => route('beneficiary.show', $beneficiary->id),
                ];
            })
            ->sortBy('client_tracking_no');
    }

    /**
     * @param string $id
     * @return Beneficiary
     */
    public function show(string $id): Beneficiary
    {
        return Beneficiary::with([
            'client',
            'client.claimant',
            'religion',
            'barangay',
        ])->findOrFail($id);
    }

    /**
     * @param string $id
     * @param array $data
     * @return void
     */
    public function update(string $id, array $data): void
    {
        $beneficiary = Beneficiary::findOrFail($id);
        $data = [
            'first_name' => $data['ben_first_name'],
            'middle_name' => $data['ben_middle_name'],
            'last_name' => $data['ben_last_name'],
            'suffix' => $data['ben_suffix'],
            'sex_id' => $data['ben_sex_id'],
            'religion_id' => $data['ben_religion_id'],
            'date_of_birth' => $data['ben_date_of_birth'],
            'date_of_death' => $data['ben_date_of_death'],
            'place_of_birth' => $data['ben_place_of_birth'],
            'barangay_id' => $data['ben_barangay_id'],
        ];
        $beneficiary->update($data);
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
