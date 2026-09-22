<?php

namespace App\Livewire\Beneficiary;

use App\Livewire\Forms\UpdateBeneficiaryForm;
use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Services\ActivityLoggerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Edit extends Component
{
    public Beneficiary $beneficiary;

    public UpdateBeneficiaryForm $form;

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;

        $this->form->setBeneficiary($beneficiary);
    }

    public function save()
    {
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
                $barangay = Barangay::find($this->form->barangayId);

                $this->beneficiary->update([
                    'first_name' => $this->form->firstName,
                    'middle_name' => $this->form->middleName,
                    'last_name' => $this->form->lastName,
                    'suffix' => $this->form->suffix,
                    'sex_id' => $this->form->sexId,
                    'religion_id' => $this->form->religionId,
                    'date_of_birth' => $this->form->dateOfBirth,
                    'date_of_death' => $this->form->dateOfDeath,
                    'pwd' => $this->form->pwd,
                    'house_no' => $this->form->houseNo,
                    'street' => $this->form->street,
                    'barangay_id' => $barangay->id,
                    'district_id' => $barangay->district_id,
                    'city' => 'Taguig City',
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully updated beneficiary\'s information',
                ]);

                ActivityLoggerService::logSuccess('Successfully updated beneficiary\'s information', [
                    'beneficiary_uuid' => $this->beneficiary->uuid,
                ]);
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to save changes to beneficiary information');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.edit');
    }
}
