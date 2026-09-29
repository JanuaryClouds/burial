<?php

namespace App\Livewire\Forms;

use App\Models\Beneficiary;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BeneficiaryForm extends Form
{
    #[Validate('required|string|max:255')]
    public ?string $firstName = null;

    #[Validate('nullable|string|max:255')]
    public ?string $middleName = null;

    #[Validate('required|string|max:255')]
    public ?string $lastName = null;

    #[Validate('nullable|string|max:255')]
    public ?string $suffix = null;

    #[Validate('required|date|before_or_equal:today')]
    public ?string $dateOfBirth = null;

    #[Validate('required|date|after_or_equal:dateOfBirth')]
    public ?string $dateOfDeath = null;

    #[Validate('nullable|boolean')]
    public ?bool $pwd = null;

    #[Validate('required|integer|exists:sexes,id')]
    public ?int $sexId = null;

    #[Validate('required|integer|exists:religions,id')]
    public ?int $religionId = null;

    #[Validate('required|string')]
    public ?string $regionCode = null;

    #[Validate('nullable|required_without:municipalityCode|string')]
    public ?string $provinceCode = null;

    #[Validate('nullable|required_without:provinceCode|string')]
    public ?string $municipalityCode = null;

    #[Validate('required|string')]
    public ?string $barangayCode = null;

    #[Validate('required|string|max:255')]
    public ?string $street = null;

    #[Validate('required|string|max:255')]
    public ?string $houseNo = null;

    #[Validate([
        'family' => ['array'],
        'family.*.name' => ['required', 'string', 'max:255'],
        'family.*.age' => ['required', 'integer'],
        'family.*.civilId' => ['required', 'string', 'max:255'],
        'family.*.sexId' => ['required', 'integer', 'exists:sexes,id'],
        'family.*.relationshipId' => ['required', 'integer', 'exists:relationships,id'],
        'family.*.occupation' => ['required', 'string', 'max:255'],
        'family.*.income' => ['required', 'numeric'],
    ])]
    public array $family = [];

    public function setBeneficiary(Beneficiary $beneficiary)
    {
        $this->firstName = $beneficiary->first_name;
        $this->middleName = $beneficiary->middle_name;
        $this->lastName = $beneficiary->last_name;
        $this->suffix = $beneficiary->suffix;
        $this->dateOfBirth = $beneficiary->date_of_birth;
        $this->dateOfDeath = $beneficiary->date_of_death;
        $this->pwd = $beneficiary->pwd;
        $this->sexId = $beneficiary->sex_id;
        $this->religionId = $beneficiary->religion_id;
        $this->regionCode = $beneficiary->region_code;
        $this->provinceCode = $beneficiary->province_code;
        $this->municipalityCode = $beneficiary->municipality_code;
        $this->barangayCode = $beneficiary->barangay_code;
        $this->street = $beneficiary->street;
        $this->houseNo = $beneficiary->house_no;
        $this->family = $beneficiary->family->toArray();
    }
}
