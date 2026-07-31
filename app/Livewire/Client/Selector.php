<?php

namespace App\Livewire\Client;

use App\Models\Beneficiary;
use App\Models\Client;
use App\Services\ClientService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Selector extends Component
{
    public array $clients;
    public ?Client $selectedClient = null;
    public ?string $selectedClientUuid = null;

    public function mount(): void
    {
        $this->draftClients();
    }

    public function draftClients(): void
    {
        $this->clients = Client::with([
            'application'
        ])
            ->whereDoesntHave('application')
            ->where('user_id', Auth::user()->id)
            ->get()
            ->sortBy('created_at')
            ->mapWithKeys(function (Client $client) {
                return [$client->uuid => $client->created_at->diffForHumans()];
            })
                ->toArray();
    }

    public function updatedSelectedClientUuid(?string $uuid): void
    {
        $this->selectedClient = $uuid
            ? Client::with(Client::relations())
                ->where('uuid', $uuid)
                ->first()
            : null;

        if ($this->selectedClient) {
            $this->dispatch('client-uuid-updated', uuid: $uuid);
        }
    }

    public function render()
    {
        return view('livewire.client.selector');
    }
}
