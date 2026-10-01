<?php

namespace App\Livewire\Forms;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ClientDemographicsForm extends Form
{
    #[Validate('required|exists:sexes,id')]
    public ?int $sexId = null;

    #[Validate('required|exists:nationalities,id')]
    public ?int $nationalityId = null;

    #[Validate('required|exists:religions,id')]
    public ?int $religionId = null;

    public function setDemographics(Client $client)
    {
        $this->sexId = $client->demographic->sex_id;
        $this->nationalityId = $client->demographic->nationality_id;
        $this->religionId = $client->demographic->religion_id;
    }
}
