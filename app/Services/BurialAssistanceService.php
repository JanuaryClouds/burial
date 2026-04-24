<?php

namespace App\Services;

use App\Models\BurialAssistance;
use Str;

class BurialAssistanceService
{
    public function index(
        ?int $user_id = null
    ) {
        return BurialAssistance::with([
            'claimant.client.user',
            'claimantChanges',
            'claimantChanges.user',
            'claimantChanges.oldClaimant.client.user',
        ])
            ->when($user_id, function ($query) use ($user_id) {
                return $query->accessibleByUser($user_id);
            })
            ->get()
            ->map(function ($application) {
                $claimantChange = $application->claimantChanges()->first();
                if ($claimantChange && $claimantChange->status == 'approved') {
                    $claimant = $application->claimantChanges()->first()->newClaimant;
                } else {
                    $claimant = $application->originalClaimant();
                }

                $status = $application->status;
                if ($status == 'approved') {
                    $status = 'For Pickup';
                }

                return [
                    'id' => $application->id,
                    'tracking_no' => $application->originalClaimant()->client?->tracking_no,
                    'claimant' => $claimant?->fullname(),
                    'contact_number' => $claimant?->contact_number,
                    'beneficiary' => $application->beneficiary()?->fullname(),
                    'status' => $status,
                    'show_route' => route('burial.show', $application),
                ];
            })
            ->sortBy('tracking_no');
    }

    public function reportIndex($startDate, $endDate)
    {
        return BurialAssistance::with([
            'claimant',
            'claimant.client',
            'claimant.client.beneficiary',
        ])
            ->whereBetween('application_date', [$startDate, $endDate])
            ->get()
            ->map(function ($burialAssistance) {
                $status = $burialAssistance->status;
                if ($status == 'approved') {
                    $status = 'For Pickup';
                }

                return [
                    'tracking_no' => $burialAssistance->claimant?->client?->tracking_no,
                    'client' => $burialAssistance->claimant?->client?->fullname(),
                    'beneficiary' => $burialAssistance->claimant?->client?->beneficiary?->fullname(),
                    'address' => $burialAssistance->claimant?->client?->address(),
                    'funeraria' => $burialAssistance->funeraria,
                    'amount' => $burialAssistance->amount,
                    'status' => Str::title($status),
                ];
            })
            ->sortBy('tracking_no');
    }

    public function columns($data)
    {
        if ($data->isEmpty()) {
            return collect();
        }

        $columns = collect(array_keys($data->first()))
            ->reject(fn ($key) => in_array($key, ['id', 'status', 'show_route']))
            ->map(fn ($key) => [
                'data' => $key,
            ])
            ->values();

        return $columns;
    }

    public function store(array $data)
    {
        return BurialAssistance::create($data);
    }

    /**
     * @param  array  $data  data to update
     * @param  BurialAssistance  $application  original application
     */
    public function update(array $data, $application)
    {
        $application->update($data);
        if (isset($data['claimant'])) {
            $application->claimant?->update($data['claimant']);
        }

        if (isset($data['beneficiary'])) {
            $application->claimant?->client?->beneficiary->update($data['beneficiary']);
        }

        // $client = $application->claimant->client;
        // $client->demographic->update([
        //     'sex_id' => $data['sex_id'],
        //     'religion_id' => $data['religion_id'],
        //     'nationality_id' => $data['nationality_id'],
        // ]);

        // $client->socialInfo->update([
        //     'relationship_id' => $data['relationship_id'],
        //     'civil_id' => $data['civil_id'],
        //     'education_id' => $data['education_id'],
        //     'income' => $data['income'],
        //     'philhealth' => $data['philhealth'],
        //     'skill' => $data['skill'],
        // ]);

        // $client->beneficiary->update([
        //     'first_name' => $data['ben_first_name'],
        //     'middle_name' => $data['ben_middle_name'],
        //     'last_name' => $data['ben_last_name'],
        //     'suffix' => $data['ben_suffix'] ?? '',
        //     'religion_id' => $data['ben_religion_id'],
        //     'barangay_id' => $data['ben_barangay_id'],
        //     'sex_id' => $data['ben_sex_id'],
        //     'date_of_birth' => $data['ben_date_of_birth'],
        //     'date_of_death' => $data['ben_date_of_death'],
        //     'place_of_birth' => $data['ben_place_of_birth'],
        // ]);

        // $families = $client->family()->orderBy('id')->get();

        // foreach ($families as $index => $family) {
        //     $family->update([
        //         'name' => $data['fam_name'][$index],
        //         'sex_id' => $data['fam_sex_id'][$index],
        //         'age' => $data['fam_age'][$index],
        //         'civil_id' => $data['fam_civil_id'][$index],
        //         'relationship_id' => $data['fam_relationship_id'][$index],
        //         'occupation' => $data['fam_occupation'][$index],
        //         'income' => $data['fam_income'][$index],
        //     ]);
        // }

        return $application;
    }

    public function delete(int $id)
    {
        $assistance = BurialAssistance::find($id);
        if ($assistance && $assistance->delete()) {
            return true;
        }

        return false;
    }
}
