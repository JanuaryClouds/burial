<?php

namespace App\Livewire\Client;

use App\Models\Client;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Component;

class Show extends Component
{
    use HasPlaceholder;

    public ?Client $client = null;

    public ?string $uuid = null;

    public function mount(?Client $client = null, ?string $uuid = null)
    {
        if ($uuid) {
            $this->client = Client::where('uuid', $uuid)->firstOrFail();
        } else {
            $this->client = $client;
        }
    }

    public function render()
    {
        return view('livewire.client.show');
    }
}
