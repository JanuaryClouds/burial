<?php

namespace App\Livewire\Client;

use App\Models\Client;
use Livewire\Component;

class Form extends Component
{
    public ?Client $client = null;

    public function mount(?Client $client = null)
    {
        $this->client = $client;
    }

    public function render()
    {
        return view('livewire.client.form');
    }
}
