<?php

namespace App\Livewire\Client;

use App\Livewire\Forms\ClientForm;
use App\Models\Barangay;
use App\Models\Client;
use App\Rules\ClientRules;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Client $client;

    public ClientForm $form;

    public function mount(Client $client)
    {
        $this->client = $client;

        $this->form->setClient($this->client);
    }

    public function save()
    {
        try {
            $this->form->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => 'Please fill up all the required fields.'
            ]);

            report($e);
            return;
        }

        try {
            DB::transaction(function () {
                $barangay = Barangay::firstWhere('id', $this->form->barangayId);

                $this->client->update([
                    'date_of_birth' => $this->form->dateOfBirth,
                    'house_no' => $this->form->houseNo,
                    'street' => $this->form->street,
                    'barangay_id' => $barangay->id,
                    'district_id' => $barangay->district_id,
                    // 'city' => $this->form->city,
                    'contact_number' => $this->form->contactNumber,
                ]);

                $this->client->demographic->update([
                    'sex_id' => $this->form->sexId,
                    'nationality_id' => $this->form->nationalityId,
                    'religion_id' => $this->form->religionId,
                ]);

                $this->client->socialInfo->update([
                    'civil_id' => $this->form->civilId,
                    'education_id' => $this->form->educationId,
                    'philhealth' => $this->form->philhealth,
                    'skill' => $this->form->skill,
                    'income' => $this->form->income,
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Client updated successfully.'
                ]);

                $this->client->refresh();
            });
        } catch (\Exception $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => 'Failed to update client. Please try again later.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.client.edit');
    }
}
