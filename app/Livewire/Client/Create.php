<?php

namespace App\Livewire\Client;

use App\Models\Barangay;
use App\Models\Client;
use App\Models\ClientDemographic;
use App\Models\ClientSocialInfo;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    // #[Rule('required|string|max:255')]
    // public string $firstName;
    
    // #[Rule('nullable|string|max:255')]
    // public ?string $middleName;
    
    // #[Rule('required|string|max:255')]
    // public string $lastName;
    
    #[Rule('nullable|string|max:255')]
    public ?string $suffix;

    #[Rule('required|date|before:now')]
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
    public ?int $educationId;

    #[Rule('nullable|string|max:255')]
    public ?string $philhealth;

    #[Rule('nullable|string|max:255')]
    public ?string $skill;

    #[Rule('nullable|string|max:255')]
    public ?string $income;

    public function render()
    {
        return view('livewire.client.create');
    }

    public function save()
    {
        $this->validate();

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

        $this->dispatch('notification:alert', [
            'type' => 'success',
            'text' => 'Successfully saved your information as a draft',
        ]);

        $this->redirect(route('beneficiary.create'));
    }
}
