<?php

namespace App\Services;

use App\Models\BurialAssistance;
use App\Models\Claimant;
use App\Models\Client;
use App\Models\ClientAssessment;
use App\Models\ClientBeneficiary;
use App\Models\ClientBeneficiaryFamily;
use App\Models\ClientDemographic;
use App\Models\ClientRecommendation;
use App\Models\ClientSocialInfo;
use App\Models\Deceased;
use App\Models\FuneralAssistance;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Str;

class ClientService
{
    private function generateTrackingNo(): string
    {
        $year = date('Y');

        $latestClient = Client::where('tracking_no', 'LIKE', $year.'-%')
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestClient) {
            $parts = explode('-', $latestClient->tracking_no);
            $lastNumber = (int) end($parts);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $year.'-'.str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    public function storeClient(array $data, string $uuid): ?Client
    {
        $tracking_no = $this->generateTrackingNo();

        $client = Client::create([
            'id' => Str::uuid(),
            'citizen_id' => $uuid,
            'tracking_no' => $tracking_no,
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'age' => $data['age'],
            'date_of_birth' => $data['date_of_birth'],
            'house_no' => $data['house_no'],
            'street' => $data['street'],
            'district_id' => $data['district_id'],
            'barangay_id' => $data['barangay_id'],
            'city' => $data['city'],
            'contact_no' => $data['contact_no'],
        ]);

        if ($client) {
            $demographic = ClientDemographic::create([
                'id' => Str::uuid(),
                'client_id' => $client->id,
                'sex_id' => $data['sex_id'],
                'religion_id' => $data['religion_id'],
                'nationality_id' => $data['nationality_id'],
            ]);

            $social = ClientSocialInfo::create([
                'id' => Str::uuid(),
                'client_id' => $client->id,
                'relationship_id' => $data['relationship_id'],
                'civil_id' => $data['civil_id'],
                'education_id' => $data['education_id'],
                'income' => $data['income'],
                'philhealth' => $data['philhealth'],
                'skill' => $data['skill'],
            ]);

            $beneficiary = ClientBeneficiary::create([
                'id' => Str::uuid(),
                'client_id' => $client->id,
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

            foreach ($data['fam_name'] as $index => $name) {
                ClientBeneficiaryFamily::create([
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'name' => $name,
                    'sex_id' => $data['fam_sex_id'][$index],
                    'age' => $data['fam_age'][$index],
                    'civil_id' => $data['fam_civil_id'][$index],
                    'relationship_id' => $data['fam_relationship_id'][$index],
                    'occupation' => $data['fam_occupation'][$index],
                    'income' => $data['fam_income'][$index],
                ]);
            }

            // $assessmentRows = [];
            // foreach($data['ass_problem_presented'] as $index => $problem)
            // {
            //     $assessmentRows[] = ClientAssessment::create([
            // 'id' => Str::uuid(),
            //         'client_id' => $client->id,
            //         'problem_presented' => $problem,
            //         'assessment' => $data['ass_assessment'][$index],
            //     ]);
            // }

            // $assistanceIds = $data['rec_assistance_id'] ?? [];
            // foreach ($assistanceIds as $assistanceId) {
            //     if ($assistanceId == 8) {
            //         ClientRecommendation::create([
            // 'id' => Str::uuid(),
            //             'client_id'     => $client->id,
            //             'assistance_id' => 8,
            //             'referral'      => $data['rec_burial_referral'][0] ?? null,
            //             'moa_id'        => $data['rec_moa'][0] ?? null,
            //             'amount'        => $data['rec_amount'][0] ?? null,
            //             'others'        => null,
            //         ]);
            //     } elseif ($assistanceId == 14) {
            //         ClientRecommendation::create([
            // 'id' => Str::uuid(),
            //             'client_id'     => $client->id,
            //             'assistance_id' => 14,
            //             'referral'      => null,
            //             'moa_id'        => null,
            //             'amount'        => null,
            //             'others'        => $data['rec_assistance_other'][0] ?? null,
            //         ]);
            //     } else {
            //         ClientRecommendation::create([
            // 'id' => Str::uuid(),
            //             'client_id'     => $client->id,
            //             'assistance_id' => $assistanceId,
            //             'referral'      => null,
            //             'moa_id'        => null,
            //             'amount'        => null,
            //             'others'        => null,
            //         ]);
            //     }
            // }

            if ($demographic && $social && $beneficiary) {
                return $client;
            } else {
                $client->delete();
            }
        }

        return null;
    }

    public function transferClient($client_id)
    {
        $client = Client::find($client_id);
        if ($client && $client->beneficiary && $client->assessment->count() > 0 && $client->recommendation->count() > 0) {
            if ($client->recommendation->first()->type == 'burial') {
                $burialAssistance = BurialAssistance::create([
                    'id' => Str::uuid(),
                    'tracking_code' => strtoupper(Str::random(6)),
                    'application_date' => $client->created_at,
                    'swa' => $client->assessment->first()->assessment,
                    'encoder' => auth()->user()->id,
                    'funeraria' => $client->recommendation->first()->referral,
                    'amount' => $client->recommendation->first()->amount,
                    'remarks' => $client->recommendation->first()->remarks,
                ]);

                $claimant = Claimant::create([
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'burial_assistance_id' => $burialAssistance->id,
                    'first_name' => $client->first_name,
                    'middle_name' => $client->middle_name ?? null,
                    'last_name' => $client->last_name,
                    'suffix' => $client->suffix ?? null,
                    'relationship_to_deceased' => $client->socialInfo->relationship->id,
                    'mobile_number' => $client->contact_no,
                    'address' => $client->house_no.' '.$client->street,
                    'barangay_id' => $client->barangay_id,
                ]);

                $deceased = Deceased::create([
                    'id' => Str::uuid(),
                    'burial_assistance_id' => $burialAssistance->id,
                    'first_name' => $client->beneficiary->first_name,
                    'middle_name' => $client->beneficiary->middle_name ?? null,
                    'last_name' => $client->beneficiary->last_name,
                    'suffix' => $client->beneficiary->suffix ?? null,
                    'date_of_birth' => $client->beneficiary->date_of_birth,
                    'date_of_death' => $client->beneficiary->date_of_death,
                    'gender' => $client->beneficiary->sex_id,
                    'address' => $client->beneficiary->place_of_birth,
                    'religion_id' => $client->beneficiary->religion_id,
                    'barangay_id' => $client->beneficiary->barangay_id,
                ]);

                return $burialAssistance;
            } elseif ($client->recommendation->first()->type == 'funeral') {
                $funeralAssistance = FuneralAssistance::create([
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'remarks' => $client->recommendation->first()->remarks,
                ]);

                return $funeralAssistance;
            }
        }

        return null;
    }

    public function updateClient(array $data, $client): Client
    {
        $client->update($data);

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

        return $client;
    }

    public function deleteClient($client): Client
    {
        return $client->delete() ? $client : null;
    }

    public function exportGIS($client)
    {
        $templatePath = storage_path('app/templates/general-intake-form.xlsx');
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue(
            'K4',
            'Date: '.Carbon::parse($client->created_at)->format('m/d/Y')
        );
        $sheet->setCellValue(
            'E7',
            $client->first_name.' '.Str::limit($client?->middle_name, 1, '.').' '.$client->last_name.($client?->suffix ? ' '.$client->suffix : '')
        );
        $sheet->setCellValue('J7', $client->age);
        $sheet->setCellValue(
            'L7',
            $client->gender == 2 ? 'Male' : 'Female'
        );
        $sheet->setCellValue('E8', Carbon::parse($client->date_of_birth)->format('F j, Y'));
        $sheet->setCellValue(
            'I8',
            $client->house_no.' '.$client->street.' '.$client->barangay->name.' ,Taguig City'
        );
        $sheet->setCellValue('F9', $client->socialInfo->relationship->name);
        $sheet->setCellValue('K9', $client->socialInfo->civil->name);
        $sheet->setCellValue('D10', $client->demographic->religion->name);
        $sheet->setCellValue('H10', $client->demographic->nationality->name);
        $sheet->setCellValue('L10', $client->socialInfo->education->name);
        $sheet->setCellValue('E11', $client->socialInfo->skill);
        $sheet->setCellValue('L11', $client->socialInfo->income);
        $sheet->setCellValue('E12', $client->socialInfo->philhealth);
        $sheet->setCellValue('J12', $client->contact_no);
        $sheet->setCellValue(
            'E16',
            $client->beneficiary->first_name.' '.Str::limit($client->beneficiary->middle_name, 1, '.').' '.$client->beneficiary->last_name.($client->beneficiary->suffix ? ' '.$client->beneficiary->suffix : '')
        );
        $sheet->setCellValue('J16', $client->beneficiary->gender == 2 ? 'Male' : 'Female');
        $sheet->setCellValue('E17', Carbon::parse($client->beneficiary->date_of_birth)->format('F j, Y'));
        $sheet->setCellValue('I17', $client->beneficiary->place_of_birth.' '.$client->beneficiary->barangay->name.' ,Taguig City');

        $rowStart = 22;
        foreach ($client->family as $member) {
            $sheet->setCellValue("B{$rowStart}", $member->name);
            $sheet->setCellValue("F{$rowStart}", $member->sex->name);
            $sheet->setCellValue("G{$rowStart}", $member->age);
            $sheet->setCellValue("H{$rowStart}", $member->civil->name);
            $sheet->setCellValue("I{$rowStart}", $member->relationship->name);
            $sheet->setCellValue("K{$rowStart}", $member->occupation);
            $sheet->setCellValue("N{$rowStart}", $member->income);
            $rowStart++;
        }

        $sheet->setCellValue(
            'B29', $client->assessment->first()->problem_presented ?? ''
        );
        $sheet->setCellValue(
            'H29', $client->assessment->first()->assessment ?? ''
        );

        if ($client->recommendation->count() > 0 && $client->recommendation->first()->moa) {
            if ($client->recommendation->first()->moa->name == 'Cash') {
                dd($client->recommendation->first()->moa->name);
                $sheet->setCellValue(
                    'I37', '✓ Cash'
                );
            } elseif ($client->recommendation->first()->moa->name == 'Check') {
                $sheet->setCellValue(
                    'J39', '✓ Check'
                );
            } elseif ($client->recommendation->first()->moa->name == 'Guarantee Letter') {
                $sheet->setCellValue(
                    'K39', '✓ Guarantee Letter'
                );
            }
        }

        if ($client->recommendation->count() > 0) {
            if ($client->recommendation->first()->type == 'funeral') {
                $sheet->setCellValue(
                    'C47', '✓'
                );
                $sheet->setCellValue(
                    'F47', 'Taguig City Public Cemetery'
                );
            } elseif ($client->recommendation->first()->type == 'burial') {
                $sheet->setCellValue(
                    'C47', '✓'
                );
                $sheet->setCellValue(
                    'F47', $client->recommendation->first()->referral
                );
                $sheet->setCellValue(
                    'J36', $client->recommendation->first()->amount
                );
            }
        }

        $sheet->setCellValue(
            'E55', $client->first_name.' '.Str::limit($client?->middle_name, 1, '.').' '.$client->last_name.($client?->suffix ? ' '.$client->suffix : '')
        );

        $sheet->setCellValue(
            'E56',
            $client->house_no.' '.$client->street.' '.$client->barangay->name.' ,Taguig City'
        );

        $filename = $client->first_name.'-'.$client->last_name.'-General-Intake-Sheet.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
