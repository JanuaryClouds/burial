<?php

namespace App\Livewire\Beneficiary\Family;

use App\Models\BeneficiaryFamily;
use App\Rules\BeneficiaryFamilyRules;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Nette\Schema\ValidationException;

class Edit extends Component
{
    public array $member;

    public ?string $name = null;

    public ?int $sexId = null;

    public ?int $relationshipId = null;

    public ?string $dateOfBirth = null;

    public ?int $civilId = null;

    public ?string $occupation = null;

    public ?float $income = null;

    public function mount(array $member)
    {
        $this->member = $member;
        $this->name = $member['name'];
        $this->sexId = $member['sex_id'];
        $this->relationshipId = $member['relationship_id'];
        $this->dateOfBirth = $member['date_of_birth'];
        $this->civilId = $member['civil_id'];
        $this->occupation = $member['occupation'];
        $this->income = $member['income'];
    }

    protected function rules(): array
    {
        return BeneficiaryFamilyRules::rules();
    }

    public function save()
    {
        try {
            $data = $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'text' => 'Please fill up all the required fields',
            ]);

            report($e);
            return;
        }

        try {
            DB::transaction(function() use ($data) {
                $this->member->update([
                    'name' => $this->name,
                    'sex_id' => $this->sexId,
                    'relationship_id' => $this->relationshipId,
                    'age' => $this->age,
                    'civil_id' => $this->civilId,
                    'occupation' => $this->occupation,
                    'income' => $this->income,
                ]);
            });

            $this->dispatch('refreshFamily');

            $this->dispatch('notification:alert', [
                'type' => 'success',
                'text' => 'Family member updated successfully',
            ]);
        } catch (\Throwable $th) {
            if (app()->hasDebugModeEnabled()) {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'text' => $th->getMessage(),
                ]);
            } else {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'text' => 'Something went wrong. Please try again later',
                ]);
            }
            
            throw $th;
        }

        $this->dispatch('refreshFamily');
    }

    public function render()
    {
        return view('livewire.beneficiary.family.edit');
    }
}
