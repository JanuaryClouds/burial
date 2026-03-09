<?php

namespace App\Services;

use App\Models\FuneralAssistance;

class FuneralAssistanceService
{
    public function index(
        string $orderColumn = 'created_at',
        string $orderDirection = 'asc',
    ){
        $allowedColumns = ['created_at', 'id'];
        $allowedDirections = ['asc', 'desc'];

        $orderColumn = in_array($orderColumn, $allowedColumns) ? $orderColumn : 'created_at';
        $orderDirection = in_array(strtolower($orderDirection), $allowedDirections) ? $orderDirection : 'asc';

        return FuneralAssistance::with(['client', 'client.beneficiary'])
            ->orderBy($orderColumn, $orderDirection)
            ->get()
            ->map(function ($application) {
                if ($application?->approved_at) {
                    $status = 'Approved';
                }

                if ($application?->forwarded_at) {
                    $status = 'Forwarded';
                }

                return [
                    'id' => $application->id,
                    'tracking_no' => $application->client?->tracking_no,
                    'client' => $application->client?->fullname(),
                    'beneficiary' => $application->client?->beneficiary?->fullname(),
                    'status' => $status ?? 'Pending',
                    'created_at' => $application->created_at->format('M d, Y'),
                    'show_route' => route('funeral.show', $application),
                ];
            });
    }

    public function reportIndex($startDate, $endDate)
    {
        return FuneralAssistance::with([
            'client',
            'client.beneficiary',
        ])->select(
            'id',
            'client_id',
            'approved_at',
            'forwarded_at',
        )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get()
            ->map(function ($funeral) {
                return [
                    'tracking_no' => $funeral->client?->tracking_no,
                    'client' => $funeral->client?->fullname(),
                    'beneficiary' => $funeral->client?->beneficiary?->fullname(),
                    'approved_at' => $funeral->approved_at ?? 'N/A',
                    'forwarded_at' => $funeral->forwarded_at ?? 'N/A',
                ];
            });
    }


    public function columns($data)
    {
        if ($data->isEmpty()) {
            return collect();
        }

        $columns = collect(array_keys($data->first()))
            ->reject(fn ($key) => in_array($key, ['id', 'status', 'show_route']))
            ->map(fn ($key) => [
                'data'  => $key,
            ])
            ->values();

        return $columns;
    }

    /**
     * @param  array  $data  Data passed from request
     * @param  FuneralAssistance  $funeralAssistance  Original application
     * @return FuneralAssistance returns the updated funeral assistance and client
     */
    public function update(array $data, $funeralAssistance)
    {
        $funeralAssistance->update($data);
        $client = $funeralAssistance->client;

        $client->demographic->update([
            'sex_id' => $data['sex_id'],
            'religion_id' => $data['religion_id'],
            'nationality_id' => $data['nationality_id'],
        ]);

        $client->socialInfo->update([
            'relationship_id' => $data['relationship_id'],
            'civil_id' => $data['civil_id'],
            'education_id' => $data['education_id'],
            'income' => $data['income'],
            'philhealth' => $data['philhealth'],
            'skill' => $data['skill'],
        ]);

        $client->beneficiary->update([
            'first_name' => $data['ben_first_name'],
            'middle_name' => $data['ben_middle_name'],
            'last_name' => $data['ben_last_name'],
            'suffix' => $data['ben_suffix'] ?? '',
            'religion_id' => $data['ben_religion_id'],
            'barangay_id' => $data['ben_barangay_id'],
            'sex_id' => $data['ben_sex_id'],
            'date_of_birth' => $data['ben_date_of_birth'],
            'date_of_death' => $data['ben_date_of_death'],
            'place_of_birth' => $data['ben_place_of_birth'],
        ]);

        $families = $client->family()->orderBy('id')->get();

        foreach ($families as $index => $family) {
            $family->update([
                'name' => $data['fam_name'][$index],
                'sex_id' => $data['fam_sex_id'][$index],
                'age' => $data['fam_age'][$index],
                'civil_id' => $data['fam_civil_id'][$index],
                'relationship_id' => $data['fam_relationship_id'][$index],
                'occupation' => $data['fam_occupation'][$index],
                'income' => $data['fam_income'][$index],
            ]);
        }

        return $funeralAssistance;
    }
}
