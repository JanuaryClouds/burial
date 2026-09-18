<?php

namespace App\Livewire\Beneficiary;

use App\Livewire\Forms\BeneficiaryForm;
use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Services\ActivityLoggerService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class Create extends Component
{
    public BeneficiaryForm $form;

    public function addFamilyMember()
    {
        if (count($this->form->family) < 5) {
            $this->form->family[] = [
                'name' => '',
                'dateOfBirth' => '',
                'civilId' => '',
                'relationshipId' => '',
                'occupation' => '',
                'income' => '',
            ];
        }
    }

    public function removeFamilyMember(int $index)
    {
        unset($this->form->family[$index]);

        $this->form->family = array_values($this->form->family);
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
                $districtId = Barangay::firstWhere('id', $this->form->barangayId)->district_id;

                $beneficiary = Beneficiary::create([
                    'created_by' => Auth::id(),
                    'first_name' => $this->form->firstName,
                    'middle_name' => $this->form->middleName,
                    'last_name' => $this->form->lastName,
                    'suffix' => $this->form->suffix,
                    'date_of_birth' => $this->form->dateOfBirth,
                    'date_of_death' => $this->form->dateOfDeath,
                    'lethal' => $this->form->lethal ?? false,
                    'pwd' => $this->form->pwd ?? false,
                    'sex_id' => $this->form->sexId,
                    'religion_id' => $this->form->religionId,
                    'barangay_id' => $this->form->barangayId,
                    'district_id' => $districtId,
                    'city' => 'Taguig City',
                    'house_no' => $this->form->houseNo,
                    'street' => $this->form->street,
                ]);

                foreach ($this->form->family as $member) {
                    $beneficiary->family()->create([
                        'name' => $member['name'],
                        'age' => $member['age'],
                        'civil_id' => $member['civilId'],
                        'sex_id' => $member['sexId'],
                        'relationship_id' => $member['relationshipId'],
                        'occupation' => $member['occupation'],
                        'income' => $member['income'],
                    ]);
                }

                session()->put('beneficiary_uuid', $beneficiary->uuid);

                $this->reset();

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Beneficiary created successfully',
                ]);

                ActivityLoggerService::logSuccess('Successfully created beneficiary', [
                    'beneficiary_uuid' => $beneficiary->uuid,
                ]);

                $this->redirect(route('application.create'));
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Unable to create beneficiary');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.create');
    }
}
