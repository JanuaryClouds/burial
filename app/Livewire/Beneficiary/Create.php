<?php

namespace App\Livewire\Beneficiary;

use App\Livewire\Forms\AddressForm;
use App\Livewire\Forms\BeneficiaryForm;
use App\Models\Barangay;
use App\Services\ActivityLoggerService;
use App\Services\AddressService;
use App\Services\BeneficiaryService;
use App\Services\PsaClassificationService;
use App\Traits\Livewire\Address\HasOptions;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    use HasOptions, HasPlaceholder;

    public BeneficiaryForm $form;

    public AddressForm $addressForm;

    public function mount()
    {
        $this->regions = app(PsaClassificationService::class)->getRegionOptions();
    }

    public function updatedAddressFormRegionCode(?string $regionCode)
    {
        $this->addressForm->reset([
            'provinceCode',
            'provinceCode_display',
            'municipalityCode',
            'municipalityCode_display',
            'barangayCode',
            'barangayCode_display',
            'street',
            'houseNumber',
        ]);

        $this->provinces = [];
        $this->municipalities = [];
        $this->barangays = [];

        if (! $regionCode) {
            return;
        }

        $this->addressForm->regionCode_display = $this->regions[$regionCode];

        $psaServices = app(PsaClassificationService::class);

        $this->provinces = $psaServices->getProvinceOptions($regionCode);

        $this->municipalities = $psaServices->getMunicipalityOptions($regionCode);
    }

    public function updatedAddressFormProvinceCode(?string $provinceCode)
    {
        $this->addressForm->reset([
            'municipalityCode',
            'municipalityCode_display',
            'barangayCode',
            'barangayCode_display',
            'houseNumber',
            'street',
        ]);

        $this->barangays = [];

        if (! $provinceCode) {
            return;
        }

        $this->addressForm->provinceCode_display = $this->provinces[$provinceCode];

        $psaServices = app(PsaClassificationService::class);

        $this->barangays = $psaServices->getBarangayOptions($provinceCode);
    }

    public function updatedAddressFormMunicipalityCode(?string $municipalityCode)
    {
        // ! The municipality code is technically the `prv` key. Using `mun` key for barangay filtering will return barangays from other municipalities.
        // * The `prv` key is being used instead because provinces and municipalities do have unique `prv` keys

        $this->addressForm->reset([
            'provinceCode',
            'provinceCode_display',
            'barangayCode',
            'barangayCode_display',
            'street',
            'houseNumber',
        ]);

        $this->barangays = [];

        if (! $municipalityCode) {
            return;
        }

        $this->addressForm->municipalityCode_display = $this->municipalities[$municipalityCode];

        $psaServices = app(PsaClassificationService::class);

        $this->barangays = $psaServices->getBarangayOptions($municipalityCode);
    }

    public function updatedAddressFormBarangayCode(?string $barangayCode)
    {
        if (! $barangayCode) {
            return;
        }

        $this->addressForm->barangayCode_display = $this->barangays[$barangayCode];

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
            $this->addressForm->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            DB::transaction(function () {
                $beneficiary = app(BeneficiaryService::class)->store($this->form->all());

                app(AddressService::class)->store(
                    $this->addressForm->all(),
                    $beneficiary
                );

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
