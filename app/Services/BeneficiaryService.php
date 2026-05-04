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
            ->whereHas('client', function ($q) {
                $q->orderBy('tracking_no', 'asc');
            })
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
            });
    }
}
