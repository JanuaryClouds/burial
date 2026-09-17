<?php

namespace App\Livewire\Beneficiary;

use App\Models\Barangay;
use App\Models\Beneficiary;
use App\Rules\BeneficiaryRules;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Edit extends Component
{
    public Beneficiary $beneficiary;

    public ?string $firstName = null;

    public ?string $middleName = null;

    public ?string $lastName = null;

    public ?string $suffix = null;

    public ?int $sexId = null;

    public ?int $religionId = null;

    #[Rule('required|date|before_or_equal:today')]
    public ?string $dateOfBirth = null;

    #[Rule('required|date|after_or_equal:dateOfBirth')]
    public ?string $dateOfDeath = null;

    public ?bool $lethal = null;
    
    public ?bool $pwd = null;
    
    public ?string $houseNo = null;

    public ?string $street = null;

    public ?int $barangayId = null;

    public ?string $city = null;

    public function mount(Beneficiary $beneficiary)
    {
        $this->beneficiary = $beneficiary;

        $this->firstName = $this->beneficiary->first_name;
        $this->middleName = $this->beneficiary->middle_name;
        $this->lastName = $this->beneficiary->last_name;
        $this->suffix = $this->beneficiary->suffix;
        $this->sexId = $this->beneficiary->sex_id;
        $this->religionId = $this->beneficiary->religion_id;
        $this->dateOfBirth = $this->beneficiary->date_of_birth;
        $this->dateOfDeath = $this->beneficiary->date_of_death;
        $this->lethal = $this->beneficiary->lethal;
        $this->pwd = $this->beneficiary->pwd;
        $this->houseNo = $this->beneficiary->house_no;
        $this->street = $this->beneficiary->street;
        $this->barangayId = $this->beneficiary->barangay_id;
        $this->city = $this->beneficiary->city;
    }

    protected function rules(): array
    {
        return BeneficiaryRules::rules();
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
            DB::transaction(function () use ($data) {
                $barangay = Barangay::find($data['barangayId']);

                $this->beneficiary->update([
                    'first_name' => $data['firstName'],
                    'middle_name' => $data['middleName'],
                    'last_name' => $data['lastName'],
                    'suffix' => $data['suffix'],
                    'sex_id' => $data['sexId'],
                    'religion_id' => $data['religionId'],
                    'date_of_birth' => $data['dateOfBirth'],
                    'date_of_death' => $data['dateOfDeath'],
                    'pwd' => $data['pwd'],
                    'lethal' => $data['lethal'],
                    'house_no' => $data['houseNo'],
                    'street' => $data['street'],
                    'barangay_id' => $barangay->id,
                    'district_id' => $barangay->district_id,
                ]);

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Successfully updated beneficiary\'s information'
                ]);
            });
        } catch (\Throwable $th) {
            if (app()->hasDebugModeEnabled()) {
                $this->dispatch('notification:toast', [
                    'type' => 'error',
                    'text' => $th->getMessage(),
                ]);
            } else {
                $this->dispatch('notification:toast', [
                    'type' => 'error',
                    'text' => 'Something went wrong. Please try again later',
                ]);
            }

            report($th);
            throw $th;
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.edit');
    }
}
