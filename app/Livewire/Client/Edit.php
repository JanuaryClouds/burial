<?php

namespace App\Livewire\Client;

use App\Livewire\Forms\ClientForm;
use App\Models\Barangay;
use App\Models\Client;
use App\Services\ActivityLoggerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
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
        if ($this->client->isClean()) {
            $this->dispatch('notification:toast', [
                'type' => 'info',
                'text' => 'No changes saved',
            ]);

            return;
        }

        try {
            $this->form->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

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
                    'text' => 'Client updated successfully.',
                ]);

                ActivityLoggerService::logSuccess('Successfully updated Client\'s information', [
                    'client_uuid' => $this->client->uuid,
                ]);

                $this->client->refresh();
            });
        } catch (\Exception $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($e, 'Failed to update client information');

            report($e);
        }
    }

    public function render()
    {
        return view('livewire.client.edit');
    }
}
