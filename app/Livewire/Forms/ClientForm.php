<?php

namespace App\Livewire\Forms;

use App\Models\Client;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ClientForm extends Form
{
    #[Validate('required|date|before:today')]
    public ?string $dateOfBirth = null;

    #[Validate('required|string|max:255')]
    public ?string $contactNumber = null;

    public function setClient(Client $client)
    {
        $this->dateOfBirth = $client->date_of_birth;
        $this->contactNumber = $client->contact_number;
    }
}
