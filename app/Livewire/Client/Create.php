<?php

namespace App\Livewire\Client;

use App\Models\Barangay;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use App\Services\CentralClientService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    public ?Client $previousRecord = null;

    // #[Rule('required|string|max:255')]
    // public string $firstName;
    
    // #[Rule('nullable|string|max:255')]
    // public ?string $middleName;
    
    // #[Rule('required|string|max:255')]
    // public string $lastName;
    
    // #[Rule('nullable|string|max:255')]
    // public ?string $suffix;

    #[Rule('required|date|before:today')]
    public string $dateOfBirth;

    #[Rule('required|exists:sexes,id')]
    public int $sexId;

    #[Rule('required|exists:civil_statuses,id')]
    public int $civilId;

    #[Rule('required|exists:nationalities,id')]
    public int $nationalityId;

    #[Rule('required|exists:religions,id')]
    public int $religionId;

    #[Rule('required|string|max:255')]
    public string $houseNo;

    #[Rule('required|string|max:255')]
    public string $street;

    #[Rule('required|exists:barangays,id')]
    public int $barangayId;

    // #[Rule('required|exists:districts,id')]
    // public int $districtId;

    #[Rule('required|string|max:255')]
    public string $city = 'Taguig City';

    #[Rule('required|string|max:255')]
    public string $contactNumber;

    #[Rule('nullable|exists:educations,id')]
    public ?int $educationId = null;

    #[Rule('nullable|string|max:255')]
    public ?string $philhealth = null;

    #[Rule('nullable|string|max:255')]
    public ?string $skill = null;

    #[Rule('nullable|string|max:255')]
    public ?string $income = null;

    public function mount()
    {
        if (Auth::user()->clients->count() > 0) {
            $this->previousRecord = Auth::user()->clients->sortByDesc('created_at')->first();
        }

        if ($this->previousRecord) {
            $this->dateOfBirth = $this->previousRecord->date_of_birth;
            $this->sexId = $this->previousRecord->demographic->sex_id;
            $this->civilId = $this->previousRecord->socialInfo->civil_id;
            $this->nationalityId = $this->previousRecord->demographic->nationality_id;
            $this->religionId = $this->previousRecord->demographic->religion_id;
            $this->houseNo = $this->previousRecord->house_no;
            $this->street = $this->previousRecord->street;
            $this->barangayId = $this->previousRecord->barangay_id;
            $this->city = $this->previousRecord->city;
            $this->contactNumber = $this->previousRecord->contact_number;
            $this->educationId = $this->previousRecord->socialInfo->education_id;
            $this->philhealth = $this->previousRecord->socialInfo->philhealth;
            $this->skill = $this->previousRecord->socialInfo->skill;
            $this->income = $this->previousRecord->socialInfo->income;

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
        $this->validate();

        try {
            DB::transaction(function () {
                $districtId = Barangay::firstWhere('id', $this->barangayId)->district_id;
        
                $client = Client::create([
                    'user_id' => Auth::id(),
                    'date_of_birth' => $this->dateOfBirth,
                    'house_no' => $this->houseNo,
                    'street' => $this->street,
                    'district_id' => $districtId,
                    'barangay_id' => $this->barangayId,
                    'city' => $this->city,
                    'contact_number' => $this->contactNumber,
                ]);
        
                ClientDemographic::create([
                    'client_uuid' => $client->uuid,
                    'sex_id' => $this->sexId,
                    'nationality_id' => $this->nationalityId,
                    'religion_id' => $this->religionId,
                ]);
                
                ClientSocialInfo::create([
                    'client_uuid' => $client->uuid,
                    'civil_id' => $this->civilId,
                    'education_id' => $this->educationId,
                    'income' => $this->income,
                    'philhealth' => $this->philhealth,
                    'skill' => $this->skill,
                ]);
        
                $this->reset();
        
                session()->put('client_uuid', $client->uuid);
        
                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully saved your information as a draft',
                ]);
        
                $this->redirect(route('beneficiary.create'));
            });
        } catch (\Throwable $th) {
            if (app()->hasDebugModeEnabled()) {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => $th->getMessage(),
                ]);
            } else {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'title' => 'Failed to Submit',
                    'text' => 'Something went wrong. Please try again.',
                ]);
            }
        }
    }
}
