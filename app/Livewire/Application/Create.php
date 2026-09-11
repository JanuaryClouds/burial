<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Create extends Component
{
    use WithFileUploads;

    public Collection $clientOptions;
    public Collection $beneficiaryOptions;

    #[Rule('required|exists:clients,uuid')]
    public string $client_uuid;

    #[Rule('required|exists:beneficiaries,uuid')]
    public string $beneficiary_uuid;

    #[Rule('required|exists:relationships,id')]
    public string $relationshipId;

    public ?Client $client = null;

    public ?Beneficiary $beneficiary = null;

    public $images = [];

    public function mount()
    {
        $this->clientOptions = Client::whereDoesntHave('application')
            ->where('user_id', '=', Auth::id())
            ->orderByDesc('created_at')
            ->get()
            ->mapWithKeys(fn($client) => [$client->uuid => $client->fullname() . ' (created in ' . Carbon::parse($client->created_at)->format('d M Y, h:i A') . ')']);

        $this->beneficiaryOptions = Beneficiary::whereDoesntHave('application')
            ->where('created_by', '=', Auth::id())
            ->orderBy('created_at')
            ->get()
            ->mapWithKeys(fn($beneficiary) => [$beneficiary->uuid => $beneficiary->fullname() . ' (created in ' . Carbon::parse($beneficiary->created_at)->format('d M Y, h:i A') . ')']);
    }

    public function removeImage(string $key)
    {
        unset($this->images[$key]);
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function () {
                $application = Application::create([
                    'client_uuid' => $this->client_uuid,
                    'beneficiary_uuid' => $this->beneficiary_uuid,
                    'relationship_id' => $this->relationshipId
                ]);

                $this->reset();

                $this->dispatch('notificaiton:alert', [
                    'type' => 'success',
                    'title' => 'Application Submitted Successfully',
                    'text' => 'Your application has been submitted. The information you have attached will no longer be available for editing.'
                ]);

                $this->redirect('application.show', $application);
            });
        } catch (\Throwable $th) {
            if (app()->hasDebugModeEnabled()) {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'title' => 'Error',
                    'text' => $th->getMessage()
                ]);
            } else {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'text' => 'Something went wrong. Try again later.'
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.application.create');
    }
}
