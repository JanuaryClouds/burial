<?php

namespace App\Livewire\Beneficiary;

use App\Livewire\Forms\AddressForm;
use App\Livewire\Forms\UpdateBeneficiaryForm;
use App\Models\Address;
use App\Models\Beneficiary;
use App\Services\ActivityLoggerService;
use App\Services\AddressService;
use App\Services\BeneficiaryService;
use App\Services\PsaClassificationService;
use App\Traits\Livewire\Address\HasOptions;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Edit extends Component
{
    use HasOptions, HasPlaceholder;

    public Beneficiary $beneficiary;

    public Address $address;

    public UpdateBeneficiaryForm $form;

    public AddressForm $addressForm;

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;
        $this->address = $beneficiary->address;

        $this->form->setBeneficiary($beneficiary);
        $this->addressForm->setAddress($beneficiary);

        $psaServices = app(PsaClassificationService::class);

        $this->regions = $psaServices->getRegionOptions();

        $this->provinces = $psaServices->getProvinceOptions(
            $this->beneficiary->address->region_code.':0:0:0'
        );
        $this->municipalities = $psaServices->getMunicipalityOptions(
            $this->beneficiary->address->region_code.':0:0:0'
        );
        $this->barangays = $psaServices->getBarangayOptions(
            $this->beneficiary->address->region_code.':'.
            ($this->beneficiary->address->province_code ?? '0').':'.
            ($this->beneficiary->address->municipality_code ?? '0').':0'
        );
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
        $this->addressForm->reset([
            'provinceCode',
            'provinceCode_display',
            'barangayCode',
            'barangayCode_display',
            'houseNumber',
            'street',
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
                app(BeneficiaryService::class)->update(
                    $this->beneficiary,
                    $this->form->all()
                );

                app(AddressService::class)->update(
                    $this->beneficiary->address,
                    $this->addressForm->all(),
                );

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully updated beneficiary\'s information',
                ]);

                ActivityLoggerService::logSuccess('Successfully updated beneficiary\'s information', [
                    'beneficiary_uuid' => $this->beneficiary->uuid,
                ]);
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to save changes to beneficiary information');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.edit');
    }
}
