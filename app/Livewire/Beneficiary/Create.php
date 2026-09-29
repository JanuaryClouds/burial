<?php

namespace App\Livewire\Beneficiary;

use App\Livewire\Forms\BeneficiaryForm;
use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Services\ActivityLoggerService;
use App\Services\PsaClassificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    public BeneficiaryForm $form;

    public array $regions = [];

    public array $provinces = [];

    public array $municipalities = [];

    public array $barangays = [];

    public function mount()
    {
        $psaServices = app(PsaClassificationService::class);

        $this->regions = collect($psaServices->getRegions())
            ->mapWithKeys(function ($item) {
                return [$item['reg'] => $item['area_name']];
            })
            ->toArray();
    }

    public function updatedFormRegionCode(?string $regionCode)
    {
        $this->form->reset(['provinceCode', 'barangayCode', 'houseNo', 'street']);

        $this->provinces = [];
        $this->municipalities = [];
        $this->barangays = [];

        if (!$regionCode) {
            return;
        }

        $psaServices = app(PsaClassificationService::class);

        $this->provinces = collect($psaServices->getProvinces($regionCode))
            ->mapWithKeys(function ($item) {
                return [$item['prv'] => $item['area_name']];
            })
            ->toArray();

        $this->municipalities = collect($psaServices->getMunicipalities($regionCode))
            ->mapWithKeys(function ($item) {
                return [$item['prv'] => $item['area_name']];
            })
            ->toArray();
    }

    public function updatedFormProvinceCode(?string $provinceCode)
    {
        $this->form->reset(['municipalityCode', 'barangayCode', 'houseNo', 'street']);

        $this->barangays = [];

        if (!$provinceCode) {
            return;
        }
        
        $psaServices = app(PsaClassificationService::class);

        $this->barangays = collect($psaServices->getBarangays($provinceCode))
            ->mapWithKeys(function ($item) {
                return [$item['bgy'] => $item['area_name']];
            })
            ->toArray();
    }

    public function updatedFormMunicipalityCode(?string $municipalityCode)
    {
        $this->form->reset(['provinceCode', 'barangayCode', 'houseNo', 'street']);

        $this->barangays = [];

        if (!$municipalityCode) {
            return;
        }
        
        $psaServices = app(PsaClassificationService::class);

        $this->barangays = collect($psaServices->getBarangays($municipalityCode))
            ->mapWithKeys(function ($item) {
                return [$item['bgy'] => $item['area_name']];
            })
            ->toArray();
    }

    public function updatedFormBarangayCode(?string $barangayCode)
    {
        // Street and house no are already enabled via readonly logic in the view
        // based on regionCode + (provinceCode || municipalityCode) being set
    }


    public function addFamilyMember()
    {
        if (count($this->form->family) < 5) {
            $this->form->family[] = [
                'name' => '',
                'dateOfBirth' => '',
                'civilId' => '',
                'relationshipId' => '',
                'occupation' => '',
                'income' => '',
            ];
        }
    }

    public function removeFamilyMember(int $index)
    {
        unset($this->form->family[$index]);

        $this->form->family = array_values($this->form->family);
    }

    public function save()
    {
        try {
            $this->form->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            DB::transaction(function () {
                $beneficiary = Beneficiary::create([
                    'created_by' => Auth::id(),
                    'first_name' => $this->form->firstName,
                    'middle_name' => $this->form->middleName,
                    'last_name' => $this->form->lastName,
                    'suffix' => $this->form->suffix,
                    'date_of_birth' => $this->form->dateOfBirth,
                    'date_of_death' => $this->form->dateOfDeath,
                    'pwd' => $this->form->pwd ?? false,
                    'sex_id' => $this->form->sexId,
                    'religion_id' => $this->form->religionId,
                    'region_code' => $this->form->regionCode,
                    'province_code' => $this->form->provinceCode,
                    'municipality_code' => $this->form->municipalityCode,
                    'barangay_code' => $this->form->barangayCode,
                    'house_no' => $this->form->houseNo,
                    'street' => $this->form->street,
                ]);

                foreach ($this->form->family as $member) {
                    $beneficiary->family()->create([
                        'name' => $member['name'],
                        'age' => $member['age'],
                        'civil_id' => $member['civilId'],
                        'sex_id' => $member['sexId'],
                        'relationship_id' => $member['relationshipId'],
                        'occupation' => $member['occupation'],
                        'income' => $member['income'],
                    ]);
                }

                session()->put('beneficiary_uuid', $beneficiary->uuid);

                $this->reset();

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Beneficiary created successfully',
                ]);

                ActivityLoggerService::logSuccess('Successfully created beneficiary', [
                    'beneficiary_uuid' => $beneficiary->uuid,
                ]);

                $this->redirect(route('application.create'));
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Unable to create beneficiary');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.create');
    }
}
