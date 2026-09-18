<?php

namespace App\Livewire\Forms;

use App\Models\BeneficiaryFamily;
use Livewire\Attributes\Validate;
use Livewire\Form;

class BeneficiaryFamilyForm extends Form
{
    #[Validate('required|string|max:255')]
    public ?string $name = null;

    #[Validate('required|int|exists:sexes,id')]
    public ?int $sexId = null;
    
    #[Validate('nullable|int')]
    public ?int $age = null;

    #[Validate('required|int|exists:civil_statuses,id')]
    public ?int $civilId = null;

    #[Validate('required|int|exists:relationships,id')]
    public ?int $relationshipId = null;

    #[Validate('nullable|string|max:255')]
    public ?string $occupation = null;

    #[Validate('nullable|string')]
    public ?string $income = null;

    public function setFamilyMember(BeneficiaryFamily $member)
    {
        $this->name = $member->name;
        $this->age = $member->age;
        $this->civilId = $member->civil_id;
        $this->relationshipId = $member->relationship_id;
        $this->sexId = $member->sex_id;
        $this->occupation = $member->occupation;
        $this->income = $member->income;
    }
}
