<?php

namespace App\Livewire\Client;

use App\Models\Barangay;
use App\Models\CivilStatus;
use App\Models\Client;
use App\Models\District;
use App\Models\Education;
use App\Models\Nationality;
use App\Models\Relationship;
use App\Models\Religion;
use App\Models\Sex;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Display extends Component
{
    public ?string $clientUuid = null;
    public ?Client $client = null;
    public ?Collection $draftClients = null;

    public function mount(?string $uuid = null, ?Collection $draftClients): void
    {
        $this->draftClients = $draftClients;
        $this->clientUuid = $uuid;

        $this->queryClient();
    }

    #[On('client-selected')]
    public function handleClientSelected(string $uuid): void
    {
        $this->clientUuid = $uuid;

        $this->queryClient();
    }
    
    public function queryClient(): void
    {
        if ($this->clientUuid && $this->draftClients) {
            $this->client = $this->draftClients->where('uuid', $this->clientUuid)
                ->first();
        } else {
            $this->client = null;
        }
    }

    public function render()
    {
        return view('livewire.client.display');
    }
}
