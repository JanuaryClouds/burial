<?php

namespace App\Services;

use App\Models\BurialAssistance;
use App\Models\Claimant;
use App\Models\Client;
use App\Models\ClientBeneficiary;
use App\Models\ClientBeneficiaryFamily;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\Deceased;
use App\Models\FuneralAssistance;
use App\Models\User;
use Carbon\Carbon;
use DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Str;

class ClientService
{
    protected $imageServices;

    public function __construct(ImageService $imageService)
    {
        $this->imageServices = $imageService;
    }

    public function index(
        string $orderColumn = 'created_at',
        string $orderDirection = 'asc',
    ) {
        return Client::with([
            'user',
            'funeralAssistance',
            'interviews',
            'assessment',
            'claimant',
            'beneficiary',
            'claimant.burialAssistance',
            'socialInfo.relationship',
        ])
            ->orWhereHas('funeralAssistance', function ($query) {
                $query->where('forwarded_at', null);
            })
            ->orWhereHas('claimant.burialAssistance', function ($query) {
                $query->where('status', '!=', 'released');
            })
            ->orWhereDoesntHave('claimant.burialAssistance')
            ->orWhereDoesntHave('funeralAssistance')
            ->orderBy($orderColumn, $orderDirection)
            ->get()
            ->map(function ($client) {
                if ($client?->interviews->count() > 0) {
                    $status = 'Interviewed';
                }

                if ($client?->assessment->count() > 0) {
                    $status = 'Assessed';
                }
                
                if (isset($client?->claimant)) {
                    $status = 'For Burial Assistance';
                }

                if (isset($client?->funeralAssistance)) {
                    $status = 'For Libreng Libing';
                }
                
                if ($client?->funeralAssistance == null || $client?->claimant == null) {
                    $show_route = route('client.show', $client);
                }
                    
                if ($client?->claimant != null) {
                    $show_route = route('burial.show', $client?->claimant?->burialAssistance);
                }

                if ($client?->funeralAssistance != null) {
                    $show_route = route('funeral.show', $client->funeralAssistance);
                }

                return [
                    'id' => $client->id,
                    'tracking_no' => $client->tracking_no,
                    'client' => $client->fullname() . ' (' . $client->socialInfo?->relationship?->name . ')',
                    'beneficiary' => $client->beneficiary?->fullname(),
                    'status' => $status ?? 'pending',
                    'created_at' => $client->created_at->format('F d, Y'),
                    'show_route' => $show_route,
                ]; 
            });
    }

    public function reportIndex($startDate, $endDate)
    {
        return Client::with(['user', 'beneficiary', 'socialInfo.relationship'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('tracking_no', 'asc')
            ->get()
            ->map(function ($client) {
                return [
                    'tracking_no' => $client->tracking_no,
                    'client' => $client->fullname() . ' (' . $client->socialInfo?->relationship?->name . ')',
                    'beneficiary' => $client->beneficiary?->fullname(),
                    'address' => $client->address(),
                    'created_at' => $client->created_at->format('F d, Y H:i'),
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
     * Summary of match
     * @param mixed $value Provided value
     * @param mixed $options System resource to match from
     * @param mixed $strict Match strictly
     */
    public function match($value, $options, $strict = false)
    {
        // Helper for fuzzy matching
        if (! $value) {
            return null;
        }
        $normalizedValue = strtolower(preg_replace('/[^a-z0-9]/i', '', $value));

        foreach ($options as $id => $name) {
            $normalizedOption = strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
            if ($normalizedValue === $normalizedOption) {
                return $id;
            }
            if ($normalizedValue == 'female') {
                return 1;
            }
            if ($normalizedValue == 'male') {
                return 2;
            }

            if ($strict) {
                // Check for contains if exact match fails (e.g. "Calzada-tipas" vs "Calzada Tipas")
                if (str_contains($normalizedOption, $normalizedValue) || str_contains($normalizedValue, $normalizedOption)) {
                    return $id;
                }
            }
        }

        return null;
    }

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

    /**
     * Summary of storeClient
     * @param array $data form data
     * @param User $user client/user submitted the form
     * @param array $images attached images of documents
     */
    public function storeClient(array $data, User $user, array $images): ?Client
    {
        return DB::transaction(function () use ($data, $user, $images) {
            $tracking_no = $this->generateTrackingNo();

            $client = Client::create([
                'id' => Str::uuid(),
                'user_id' => $user->id,
                'tracking_no' => $tracking_no,
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

                if (! $demographic) {
                    throw new \RuntimeException('Failed to create client related records');
                }

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

                if (! $social) {
                    throw new \RuntimeException('Failed to create client related records');
                }

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

                if (! $beneficiary) {
                    throw new \RuntimeException('Failed to create client related records');
                }

                if (is_array($data['fam_name']) && count($data['fam_name']) > 0) {
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
                }

                foreach ($images as $fieldName => $uploadedFile) {
                    $this->imageServices->post($fieldName, $uploadedFile);    
                }
                dd('Catch');

                return $client;
            }
        });
    }

    public function get(string $id)
    {
        return Client::with([
            'user',
            'assessment',
            'beneficiary',
            'family',
            'demographic',
            'socialInfo',
            'recommendation',
            'interviews',
            'barangay'
        ])
            ->findOrFail($id);
    }

    public function transferClient($client_id)
    {
        $client = Client::find($client_id);
        if ($client && $client->beneficiary && $client->assessment->count() > 0 && $client->recommendation->count() > 0) {
            if ($client->recommendation->first()->type == 'burial') {
                $burialAssistance = BurialAssistance::create([
                    'id' => Str::uuid(),
                    'application_date' => $client->created_at,
                    'swa' => $client->assessment->first()->assessment,
                    'encoder' => auth()->user()->id,
                    'funeraria' => $client->recommendation->first()->referral,
                    'amount' => $client->recommendation->first()->amount,
                    'remarks' => $client->recommendation->first()->remarks,
                    'initial_checker' => auth()->user()->id, // TODO ask who is the initial checker
                ]);

                $claimant = Claimant::create([
                    'id' => Str::uuid(),
                    'client_id' => $client->id,
                    'burial_assistance_id' => $burialAssistance->id,
                    'first_name' => $client->user->first_name,
                    'middle_name' => $client->user->middle_name ?? null,
                    'last_name' => $client->user->last_name,
                    'suffix' => $client->user->suffix ?? null,
                    'relationship_to_deceased' => $client->socialInfo->relationship->id,
                    'mobile_number' => $client->user->contact_number,
                    'address' => $client->house_no.' '.$client->street,
                    'barangay_id' => $client->barangay_id,
                ]);

                // TODO use ClientBeneficiary model instead
                // $deceased = Deceased::create([
                //     'id' => Str::uuid(),
                //     'burial_assistance_id' => $burialAssistance->id,
                //     'first_name' => $client->beneficiary->first_name,
                //     'middle_name' => $client->beneficiary->middle_name ?? null,
                //     'last_name' => $client->beneficiary->last_name,
                //     'suffix' => $client->beneficiary->suffix ?? null,
                //     'date_of_birth' => $client->beneficiary->date_of_birth,
                //     'date_of_death' => $client->beneficiary->date_of_death,
                //     'gender' => $client->beneficiary->sex_id,
                //     'address' => $client->beneficiary->place_of_birth,
                //     'religion_id' => $client->beneficiary->religion_id,
                //     'barangay_id' => $client->beneficiary->barangay_id,
                // ]);

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
            $client->fullname()
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
        $sheet->setCellValue('J12', $client->user->contact_number);
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
            'E55', $client->fullname());

        $sheet->setCellValue(
            'E56',
            $client->address()
        );

        $filename = $client->fullname().'-General-Intake-Sheet.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
