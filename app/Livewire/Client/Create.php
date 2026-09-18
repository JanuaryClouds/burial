<?php

namespace App\Livewire\Client;

use App\Livewire\Forms\ClientForm;
use App\Models\Barangay;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Models\DocumentRequirement;
use App\Rules\ClientRules;
use App\Services\ActivityLoggerService;
use App\Services\CentralClientService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    public ?Client $previousRecord = null;

    public ClientForm $form;

    public array $requiredDocuments;

    public function mount()
    {
        $this->requiredDocuments = DocumentRequirement::burial();

        if (Auth::user()->clients->count() > 0) {
            $this->previousRecord = Auth::user()->clients->sortByDesc('created_at')->first()
                ->loadMissing(['demographic', 'socialInfo']);
        }

        if ($this->previousRecord) {
            $this->form->setClient($this->previousRecord);

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'title' => 'Previous Record Found',
                'text' => 'Successfully loaded your previous record and autofilled out the fields.'
            ]);
        } else {
            $this->dispatch('notification:alert', [
                'type' => 'info',
                'title' => 'No Previous Record Found',
                'text' => 'No previous record found. Please fill out the fields.'
            ]);
        }
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
                $districtId = Barangay::firstWhere('id', $this->form->barangayId)->district_id;
        
                $client = Client::create([
                    'user_id' => Auth::id(),
                    'date_of_birth' => $this->form->dateOfBirth,
                    'house_no' => $this->form->houseNo,
                    'street' => $this->form->street,
                    'district_id' => $districtId,
                    'barangay_id' => $this->form->barangayId,
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
                    'client_uuid' => $client->uuid
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
