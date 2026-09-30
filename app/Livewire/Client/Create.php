<?php

namespace App\Livewire\Client;

use App\Livewire\Forms\AddressForm;
use App\Livewire\Forms\ClientForm;
use App\Models\Address;
use App\Models\Barangay;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\DocumentRequirement;
use App\Services\ActivityLoggerService;
use App\Services\PsaClassificationService;
use App\Traits\Livewire\HasPlaceholder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    use HasPlaceholder;

    public ?Client $previousRecord = null;

    public ClientForm $form;

    public AddressForm $addressForm;

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

        if (Auth::user()->clients->count() > 0) {
            $this->previousRecord = Auth::user()->clients->sortByDesc('created_at')->first()
                ->loadMissing(['demographic', 'socialInfo', 'address']);
        }

        if ($this->previousRecord) {
            $this->form->setClient($this->previousRecord);

            $this->addressForm->setAddress($this->previousRecord);

            // Autofill the dropdown option arrays from the previous record
            // so the live selects render the saved values in their dropdowns.
            if ($this->previousRecord->address) {
                $this->provinces = collect(app(PsaClassificationService::class)->getProvinces($this->previousRecord->address->region_code))
                    ->mapWithKeys(function ($item) {
                        return [$item['prv'] => $item['area_name']];
                    })
                    ->toArray();

                $this->municipalities = collect(app(PsaClassificationService::class)->getMunicipalities($this->previousRecord->address->region_code))
                    ->mapWithKeys(function ($item) {
                        return [$item['mun'] => $item['area_name']];
                    })
                    ->toArray();

                if ($this->previousRecord->address->barangay_code) {
                    $this->barangays = collect(app(PsaClassificationService::class)->getBarangays($this->previousRecord->address->province_code))
                        ->mapWithKeys(function ($item) {
                            return [$item['bgy'] => $item['area_name']];
                        })
                        ->toArray();
                }
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
            'proviceCode', 
            'proviceCode_display', 
            'municipalityCode', 
            'municipalityCode_display', 
            'barangayCode', 
            'barangayCode_display', 
            'street', 
            'houseNumber'
        ]);

        $this->provinces = [];
        $this->municipalities = [];
        $this->barangays = [];

        if (!$regionCode) {
            return;
        }

        $this->addressForm->regionCode_display = $this->regions[$regionCode];

        $psaServices = app(PsaClassificationService::class);

        $this->provinces = collect($psaServices->getProvinces($regionCode))
            ->mapWithKeys(function ($item) {
                return [$item['prv'] => $item['area_name']];
            })
            ->toArray();

        $this->municipalities = collect($psaServices->getMunicipalities($regionCode))
            ->mapWithKeys(function ($item) {
                return [$item['mun'] => $item['area_name']];
            })
            ->toArray();
    }

    public function updatedAddressFormProvinceCode(?string $provinceCode)
    {
        $this->addressForm->reset([
            'municipalityCode', 
            'municipalityCode_display', 
            'barangayCode', 
            'barangayCode_display', 
            'houseNumber', 
            'street'
        ]);

        $this->barangays = [];

        if (!$provinceCode) {
            return;
        }

        $this->addressForm->provinceCode_display = $this->provinces[$provinceCode];
        
        $psaServices = app(PsaClassificationService::class);

        $this->barangays = collect($psaServices->getBarangays($provinceCode))
            ->mapWithKeys(function ($item) {
                return [$item['bgy'] => $item['area_name']];
            })
            ->toArray();
    }

    public function updatedAddressFormMunicipalityCode(?string $municipalityCode)
    {
        $this->addressForm->reset([
            'provinceCode', 
            'provinceCode_display', 
            'barangayCode',
            'barangayCode_display', 
            'houseNumber', 
            'street'
        ]);

        $this->barangays = [];

        if (!$municipalityCode) {
            return;
        }

        $this->addressForm->municipalityCode_display = $this->municipalities[$municipalityCode];
        
        $psaServices = app(PsaClassificationService::class);

        $this->barangays = collect($psaServices->getBarangays($municipalityCode))
            ->mapWithKeys(function ($item) {
                return [$item['bgy'] => $item['area_name']];
            })
            ->toArray();
    }

    public function updatedAddressFormBarangayCode(?string $barangayCode)
    {
        if (!$barangayCode) {
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
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }
        
        try {
            DB::transaction(function () {
                $client = Client::create([
                    'user_id' => Auth::id(),
                    'date_of_birth' => $this->form->dateOfBirth,
                    'contact_number' => $this->form->contactNumber,
                ]);

                Address::create([
                    'addressable_type' => Client::class,
                    'addressable_id' => $client->uuid,
                    'region_code' => $this->addressForm->regionCode,
                    'region_name' => $this->addressForm->regionCode_display,
                    'province_code' => $this->addressForm->provinceCode,
                    'province_name' => $this->addressForm->provinceCode_display,
                    'municipality_code' => $this->addressForm->municipalityCode,
                    'municipality_name' => $this->addressForm->municipalityCode_display,
                    'barangay_code' => $this->addressForm->barangayCode,
                    'barangay_name' => $this->addressForm->barangayCode_display,
                    'street' => $this->addressForm->street,
                    'house_number' => $this->addressForm->houseNumber,
                ]);

                ClientDemographic::create([
                    'client_uuid' => $client->uuid,
                    'sex_id' => $this->form->sexId,
                    'nationality_id' => $this->form->nationalityId,
                    'religion_id' => $this->form->religionId,
                ]);

                ClientSocialInfo::create([
                    'client_uuid' => $client->uuid,
                    'civil_id' => $this->form->civilId,
                    'education_id' => $this->form->educationId,
                    'income' => $this->form->income,
                    'philhealth' => $this->form->philhealth,
                    'skill' => $this->form->skill,
                ]);


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
