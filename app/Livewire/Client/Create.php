<?php

namespace App\Livewire\Client;

use App\Livewire\Forms\ClientForm;
use App\Models\Barangay;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\DocumentRequirement;
use App\Services\ActivityLoggerService;
use App\Services\PsaClassificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    public ?Client $previousRecord = null;

    public ClientForm $form;

    public array $regions = [];

    public array $provinces = [];

    public array $municipalities = [];

    public array $barangays = [];

    public array $requiredDocuments;

    public function mount()
    {
        $this->requiredDocuments = DocumentRequirement::burial();

        $psaServices = app(PsaClassificationService::class);

        $this->regions = collect($psaServices->getRegions())
            ->mapWithKeys(function ($item) {
                return [$item['reg'] => $item['area_name']];
            })
            ->toArray();

        if (Auth::user()->clients->count() > 0) {
            $this->previousRecord = Auth::user()->clients->sortByDesc('created_at')->first()
                ->loadMissing(['demographic', 'socialInfo']);
        }

        if ($this->previousRecord) {
            $this->form->setClient($this->previousRecord);

            // Autofill the dropdown option arrays from the previous record
            // so the live selects render the saved values in their dropdowns.
            if ($this->previousRecord->region_code) {
                $this->provinces = collect(app(PsaClassificationService::class)->getProvinces($this->previousRecord->region_code))
                    ->mapWithKeys(function ($item) {
                        return [$item['prv'] => $item['area_name']];
                    })
                    ->toArray();

                $this->municipalities = collect(app(PsaClassificationService::class)->getMunicipalities($this->previousRecord->region_code))
                    ->mapWithKeys(function ($item) {
                        return [$item['prv'] => $item['area_name']];
                    })
                    ->toArray();

                if ($this->previousRecord->barangay_code) {
                    $this->barangays = collect(app(PsaClassificationService::class)->getBarangays($this->previousRecord->province_code))
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

    public function updatedFormRegionCode(?string $regionCode)
    {
        $this->form->reset(['proviceCode', 'municipalityCode', 'barangayCode', 'street', 'houseNo']);

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

    public function render()
    {
        return view('livewire.client.create');
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
                $client = Client::create([
                    'user_id' => Auth::id(),
                    'date_of_birth' => $this->form->dateOfBirth,
                    'region_code' => $this->form->regionCode,
                    'province_code' => $this->form->provinceCode,
                    'municipality_code' => $this->form->municipalityCode,
                    'barangay_code' => $this->form->barangayCode,
                    'street' => $this->form->street,
                    'house_no' => $this->form->houseNo,
                    'city' => 'Taguig City',
                    'contact_number' => $this->form->contactNumber,
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
