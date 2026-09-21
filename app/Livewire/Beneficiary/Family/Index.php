<?php

namespace App\Livewire\Beneficiary\Family;

use App\Livewire\Forms\BeneficiaryFamilyForm;
use App\Models\Beneficiary;
use App\Models\BeneficiaryFamily;
use App\Services\ActivityLoggerService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public Beneficiary $beneficiary;

    public BeneficiaryFamilyForm $form;

    public Collection $family;

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;
        $this->family = $this->beneficiary->family;
    }

    public function placeholder()
    {
        return view('components.card.loading');
    }

    #[On('refreshFamily')]
    public function refreshFamily()
    {
        $this->beneficiary->refresh();
        $this->beneficiary->load('family');

        $this->family = $this->beneficiary->family;
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
                BeneficiaryFamily::create([
                    'beneficiary_uuid' => $this->beneficiary->uuid,
                    'name' => $this->form->name,
                    'sex_id' => $this->form->sexId,
                    'age' => $this->form->age,
                    'civil_id' => $this->form->civilId,
                    'relationship_id' => $this->form->relationshipId,
                    'occupation' => $this->form->occupation,
                    'income' => $this->form->income,
                ]);

                $this->form->reset();
                $this->dispatch('refreshFamily');
                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Family member added successfully',
                ]);
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to add family member to beneficiary');

            throw ($th);
        }
    }

    public function removeFamilyMember(string $uuid)
    {
        $member = BeneficiaryFamily::firstWhere('uuid', $uuid);

        try {
            DB::transaction(function () use ($member) {
                if ($member) {
                    $member->delete();

                    ActivityLoggerService::logSuccess('Successfully removed family member from beneficiary', [
                        'beneficiary_uuid' => $this->beneficiary->uuid,
                        'member_uuid' => $member->uuid,
                    ]);

                    $this->dispatch('notification:toast', [
                        'type' => 'success',
                        'text' => 'Family member removed successfully',
                    ]);
                }
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Failed to remove family member from beneficiary');

            report($th);
        }

        $this->dispatch('refreshFamily');
    }

    public function render()
    {
        return view('livewire.beneficiary.family.index');
    }
}
