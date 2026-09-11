<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Models\Beneficiary;
use App\Models\Client;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
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
