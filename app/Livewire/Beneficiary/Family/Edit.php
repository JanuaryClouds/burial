<?php

namespace App\Livewire\Beneficiary\Family;

use App\Livewire\Forms\BeneficiaryFamilyForm;
use App\Models\BeneficiaryFamily;
use App\Services\ActivityLoggerService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Nette\Schema\ValidationException;

class Edit extends Component
{
    public BeneficiaryFamily $member;

    public BeneficiaryFamilyForm $form;

    public function mount(BeneficiaryFamily $member)
    {
        $this->member = $member->loadMissing([
            'beneficiary.application',
        ]);

        $this->form->setFamilyMember($member);
    }

    public function save()
    {
        if ($this->member->isClean()) {
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
                $this->member->update([
                    'name' => $this->form->name,
                    'sex_id' => $this->form->sexId,
                    'age' => $this->form->age,
                    'civil_id' => $this->form->civilId,
                    'relationship_id' => $this->form->relationshipId,
                    'occupation' => $this->form->occupation,
                    'income' => $this->form->income,
                ]);
            });

            $this->dispatch('notification:toast', [
                'type' => 'success',
                'text' => 'Family member saved successfully',
            ]);

            ActivityLoggerService::logSuccess('Successfully saved Beneficiary Family Member\'s Information', [
                'beneficiary_family_member_uuid' => $this->member->uuid,
            ]);
        } catch (\Throwable $th) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to save beneficiary family member information');

            report($th);
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.family.edit');
    }
}
