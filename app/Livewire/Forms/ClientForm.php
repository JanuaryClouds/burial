<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ClientForm extends Form
{
    #[Validate('required|date|before:today')]
    public ?string $dateOfBirth = null;

    #[Validate('required|exists:sexes,id')]
    public ?int $sexId = null;

    #[Validate('required|exists:civil_statuses,id')]
    public ?int $civilId = null;

    #[Validate('required|exists:nationalities,id')]
    public ?int $nationalityId = null;

    #[Validate('required|exists:religions,id')]
    public ?int $religionId = null;

    #[Validate('required|string|max:255')]
    public ?string $houseNo = null;

    #[Validate('required|string|max:255')]
    public ?string $street = null;

    #[Validate('required|exists:barangays,id')]
    public ?int $barangayId = null;

    // #[Validate('required|exists:districts,id')]
    // public int $districtId;

    // #[Validate('required|string|max:255')]
    // public ?string $city = 'Taguig City';

    #[Validate('required|string|max:255')]
    public ?string $contactNumber = null;

    #[Validate('nullable|exists:educations,id')]
    public ?int $educationId = null;

    #[Validate('nullable|string|max:255')]
    public ?string $philhealth = null;

    #[Validate('nullable|string|max:255')]
    public ?string $skill = null;

    #[Validate('nullable|string|max:255')]
    public ?string $income = null;

    // public static function rules(): array
    // {
    //     return [
    //         'dateOfBirth' => ['required', 'date', 'before:today'],
    //         'sexId' => ['required', 'exists:sexes,id'],
    //         'civilId' => ['required', 'exists:civil_statuses,id'],
    //         'nationalityId' => ['required', 'exists:nationalities,id'],
    //         'religionId' => ['required', 'exists:religions,id'],
    //         'houseNo' => ['required', 'string', 'max:255'],
    //         'street' => ['required', 'string', 'max:255'],
    //         'barangayId' => ['required', 'exists:barangays,id'],
    //         // 'city' => ['required', 'string', 'max:255'],
    //         'contactNumber' => ['required', 'string', 'max:255'],
    //         'educationId' => ['nullable', 'exists:educations,id'],
    //         'philhealth' => ['nullable', 'string', 'max:255'],
    //         'skill' => ['nullable', 'string', 'max:255'],
    //         'income' => ['nullable', 'string', 'max:255'],
    //     ];
    // }

    public function setClient($client)
    {
        $this->dateOfBirth = $client->date_of_birth;
        $this->sexId = $client->demographic->sex_id;
        $this->civilId = $client->socialInfo->civil_id;
        $this->nationalityId = $client->demographic->nationality_id;
        $this->religionId = $client->demographic->religion_id;
        $this->houseNo = $client->house_no;
        $this->street = $client->street;
        $this->barangayId = $client->barangay_id;
        // $this->form->city = $this->previousRecord->city;
        $this->contactNumber = $client->contact_number;
        $this->educationId = $client->socialInfo->education_id;
        $this->philhealth = $client->socialInfo->philhealth;
        $this->skill = $client->socialInfo->skill;
        $this->income = $client->socialInfo->income;
    }
}
