<?php

namespace App\Services;

use App\Models\Beneficiary;

class BeneficiaryService
{
    public function index()
    {
        return Beneficiary::with([
            'client',
            'client.claimant',
            'client.funeralAssistance',
            'religion',
        ])
            ->get()
            ->map(function ($beneficiary) {
                $assistance = 'Pending';
                if ($beneficiary->client?->claimant?->count() > 0) {
                    $assistance = 'Burial Assistance';
                }

                if ($beneficiary->client?->funeralAssistance?->count() > 0) {
                    $assistance = 'Funeral Assistance';
                }

                return [
                    'client_tracking_no' => $beneficiary->client?->tracking_no,
                    'beneficiary' => $beneficiary->fullname(),
                    'date_of_birth' => $beneficiary->date_of_birth,
                    'date_of_death' => $beneficiary->date_of_death,
                    'religion' => $beneficiary->religion?->name,
                    'assistance' => $assistance,
                ];
            })
            ->sortBy('client_tracking_no');
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
            ->get()
            ->map(function ($beneficiary) {
                $assistance = 'Pending';
                if ($beneficiary->client?->claimant?->count() > 0) {
                    $assistance = 'Burial Assistance';
                }

                if ($beneficiary->client?->funeralAssistance?->count() > 0) {
                    $assistance = 'Funeral Assistance';
                }

                return [
                    'client_tracking_no' => $beneficiary->client?->tracking_no,
                    'beneficiary' => $beneficiary->fullname(),
                    'date_of_birth' => $beneficiary->date_of_birth,
                    'date_of_death' => $beneficiary->date_of_death,
                    'religion' => $beneficiary->religion?->name,
                    'assistance' => $assistance,
                ];
            })
            ->sortBy('client_tracking_no');
    }
}
