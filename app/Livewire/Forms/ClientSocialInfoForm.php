<?php

namespace App\Livewire\Forms;

use App\Models\Client;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ClientSocialInfoForm extends Form
{
    #[Validate('required|exists:civil_statuses,id')]
    public ?int $civilId = null;

    #[Validate('required|exists:educations,id')]
    public ?int $educationId = null;

    #[Validate('nullable|string|max:255')]
    public ?string $philhealth = null;

    #[Validate('nullable|string|max:255')]
    public ?string $skill = null;

    #[Validate('nullable|string|max:255')]
    public ?string $income = null;

    public function setSocialInfo(Client $client)
    {
        $this->civilId = $client->socialInfo->civil_id;
        $this->educationId = $client->socialInfo->education_id;
        $this->income = $client->socialInfo->income;
        $this->philhealth = $client->socialInfo->philhealth;
        $this->skill = $client->socialInfo->skill;
    }
}
