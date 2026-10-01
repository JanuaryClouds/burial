<?php

namespace App\Livewire\Client;

use App\Livewire\Forms\AddressForm;
use App\Livewire\Forms\ClientDemographicsForm;
use App\Livewire\Forms\ClientForm;
use App\Livewire\Forms\ClientSocialInfoForm;
use App\Models\Address;
use App\Models\Barangay;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Services\ActivityLoggerService;
use App\Services\AddressService;
use App\Services\ClientDemographicsService;
use App\Services\ClientService;
use App\Services\ClientSocialInfoService;
use App\Services\PsaClassificationService;
use App\Traits\Livewire\Address\HasOptions;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Edit extends Component
{
    use HasPlaceholder, HasOptions;

    public Client $client;

    public ClientDemographic $demographic;

    public ClientSocialInfo $socialInfo;

    public Address $address;

    public ClientDemographicsForm $demographicForm;

    public ClientSocialInfoForm $socialInfoForm;

    public ClientForm $form;

    public AddressForm $addressForm;

    public function mount(Client $client)
    {
        $psaServices = app(PsaClassificationService::class);

        $this->client = $client;
        $this->address = $client->address;
        $this->demographic = $client->demographic;
        $this->socialInfo = $client->socialInfo;

        $this->form->setClient($this->client);
        $this->demographicForm->setDemographics($this->client);
        $this->socialInfoForm->setSocialInfo($this->client);
        $this->addressForm->setAddress($this->client);

        $this->regions = $psaServices->getRegionOptions();

        $this->provinces = $psaServices->getProvinceOptions(
            $this->client->address->region_code . ':0:0:0'
        );
        $this->municipalities = $psaServices->getMunicipalityOptions(
            $this->client->address->region_code . ':0:0:0'
        );
        $this->barangays = $psaServices->getBarangayOptions(
            $this->client->address->region_code . ':' .
            ($this->client->address->province_code ?? '0') . ':' .
            ($this->client->address->municipality_code ?? '0') . ':0'
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
            $this->demographicForm->validate();
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
                app(ClientService::class)->update(
                    $this->client,
                    $this->form->all()
                );

                app(AddressService::class)->update(
                    $this->client->address,
                    $this->addressForm->all()
                );

                app(ClientDemographicsService::class)->update(
                    $this->client->demographic,
                    $this->demographicForm->all()
                );

                app(ClientSocialInfoService::class)->update(
                    $this->client->socialInfo,
                    $this->socialInfoForm->all()
                );

                ActivityLoggerService::logSuccess('Successfully updated Client\'s information', [
                    'client_uuid' => $this->client->uuid,
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully updated Client\'s information',
                ]);

                $this->client->refresh();
            });
        } catch (\Exception $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($e, 'Failed to update client information');

            report($e);
        }
    }

    public function render()
    {
        return view('livewire.client.edit');
    }
}
