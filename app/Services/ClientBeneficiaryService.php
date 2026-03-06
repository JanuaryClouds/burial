<?php

namespace App\Services;

use App\Models\ClientBeneficiary;

class ClientBeneficiaryService
{
    public function index()
    {
        return ClientBeneficiary::with([
            'client',
            'client.claimant',
            'client.funeralAssistance',
        ])
            ->get()
            ->map(function ($clientBeneficiary) {
                $assistance = 'Pending';
                if ($clientBeneficiary->client->claimant->count() > 0) {
                    $assistance = 'Burial Assistance';
                }

                if ($clientBeneficiary->client->funeralAssistance->count() > 0) {
                    $assistance = 'Funeral Assistance';
                }
                return [
                    'tracking_no' => $clientBeneficiary->client->tracking_no,
                    'beneficiary' => $clientBeneficiary->fullname(),
                    'date_of_birth' => $clientBeneficiary->date_of_birth,
                    'date_of_death' => $clientBeneficiary->date_of_death,
                    'religion' => $clientBeneficiary->religion->name,
                    'assistance' => $assistance,
                ];
            })
            ->sortBy('tracking_no');
    }
}