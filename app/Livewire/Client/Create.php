<?php

namespace App\Livewire\Client;

use App\Livewire\Forms\AddressForm;
use App\Livewire\Forms\ClientDemographicsForm;
use App\Livewire\Forms\ClientForm;
use App\Livewire\Forms\ClientSocialInfoForm;
use App\Models\Client;
use App\Services\ActivityLoggerService;
use App\Services\AddressService;
use App\Services\ClientDemographicsService;
use App\Services\ClientService;
use App\Services\ClientSocialInfoService;
use App\Services\PsaClassificationService;
use App\Traits\Livewire\Address\HasOptions;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    use HasOptions, HasPlaceholder;

    public ?Client $previousRecord = null;

    public ClientForm $form;

    public AddressForm $addressForm;

    public ClientDemographicsForm $demographicsForm;

    public ClientSocialInfoForm $socialInfoForm;

    public function mount()
    {
        $psaServices = app(PsaClassificationService::class);

        $this->regions = $psaServices->getRegionOptions();

        if (Auth::user()->clients->count() > 0) {
            $this->previousRecord = Auth::user()->clients->sortByDesc('created_at')->first()
                ->loadMissing(['demographic', 'socialInfo', 'address']);
        }

        if ($this->previousRecord) {
            $this->form->setClient($this->previousRecord);
            $this->addressForm->setAddress($this->previousRecord);
            $this->demographicsForm->setDemographics($this->previousRecord);
            $this->socialInfoForm->setSocialInfo($this->previousRecord);

            // Autofill the dropdown option arrays from the previous record
            // so the live selects render the saved values in their dropdowns.
            if ($this->previousRecord->address) {
                $this->provinces = $psaServices->getProvinceOptions(
                    $this->previousRecord->address->region_code.':0:0:0'
                );
                $this->municipalities = $psaServices->getMunicipalityOptions(
                    $this->previousRecord->address->region_code.':0:0:0'
                );
                $this->barangays = $psaServices->getBarangayOptions(
                    $this->previousRecord->address->region_code.':'.
                    ($this->previousRecord->address->province_code ?? '0').':'.
                    ($this->previousRecord->address->municipality_code ?? '0').':0'
                );
            }

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'title' => 'Previous Record Found',
                'text' => 'Successfully loaded your previous record and autofilled out the fields.',
            ]);
        } else {
            $this->dispatch('notification:alert', [
                'type' => 'info',
                'title' => 'No Previous Record Found',
                'text' => 'No previous record found. Please fill out the fields.',
            ]);
        }
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

    public function render()
    {
        return view('livewire.client.create');
    }

    public function save()
    {
        try {
            $this->form->validate();
            $this->addressForm->validate();
            $this->demographicsForm->validate();
            $this->socialInfoForm->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            DB::transaction(function () {
                $client = app(ClientService::class)->store(
                    $this->form->all()
                );

                app(AddressService::class)->store(
                    $this->addressForm->all(),
                    $client
                );

                app(ClientDemographicsService::class)->store(
                    $this->demographicsForm->all(),
                    $client
                );

                app(ClientSocialInfoService::class)->store(
                    $this->socialInfoForm->all(),
                    $client
                );

                $this->reset();

                session()->put('client_uuid', $client->uuid);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully saved your information as a draft',
                ]);

                ActivityLoggerService::logSuccess('Successfuly saved client', [
                    'client_uuid' => $client->uuid,
                ]);

                $this->redirect(route('beneficiary.create'));
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => $th->getMessage(),
            ]);

            ActivityLoggerService::logException($th, 'Failed to create client');

            report($th);
        }
    }
}
