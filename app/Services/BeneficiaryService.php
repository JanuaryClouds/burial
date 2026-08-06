<?php

namespace App\Services;

use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;

class BeneficiaryService
{
    public function index(?string $user_id = null, string $orderBy = 'created_at', string $orderDirection = 'asc')
    {
        return Beneficiary::with([
            'application',
            'application.client',
            'application.client.user',
            'application.client.interviews',
            'application.assessment',
            'application.recommendations',
            'application.recommendations.assistance',
            'application.referral',
            'application.processLogs',
            'religion',
        ])
            ->when($user_id, function ($query) use ($user_id) {
                $query->where('created_by', $user_id);
            })
            ->orderBy($orderBy, $orderDirection)
            ->get()
            ->map(function (Beneficiary $beneficiary) {
                $application = $beneficiary->application;
                $status = $application ? $application->status() : 'Draft';

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

    public function update(array $data, Beneficiary $beneficiary): void
    {
        $beneficiary->update($data);
    }

    public function store(array $data)
    {
        $data['created_by'] = Auth::user()->id;

        return Beneficiary::create($data);
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
