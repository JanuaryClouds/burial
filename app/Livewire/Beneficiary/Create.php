<?php

namespace App\Livewire\Beneficiary;

use App\Models\Barangay;
use App\Models\Beneficiary;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Rule;
use Livewire\Component;

class Create extends Component
{
    #[Rule('required|string|max:255')]
    public string $firstName;

    #[Rule('nullable|string|max:255')]
    public ?string $middleName = null;

    #[Rule('required|string|max:255')]
    public string $lastName;

    #[Rule('nullable|string|max:255')]
    public ?string $suffix = null;

    #[Rule('required|date|before_or_equal:tomorrow')]
    public string $dateOfBirth;

    #[Rule('required|date|after_or_equal:dateOfBirth|before_or_equal:tomorrow')]
    public string $dateOfDeath;

    #[Rule('nullable|boolean')]
    public ?bool $lethal;

    #[Rule('nullable|boolean')]
    public ?bool $pwd;

    #[Rule('required|integer')]
    public int $sexId;

    #[Rule('nullable|integer')]
    public int $religionId;

    #[Rule('required|integer')]
    public int $barangayId;

    // #[Rule('required|integer')]
    // public int $districtId;

    // #[Rule('required|string|max:255')]
    // public string $city;

    #[Rule('required|string|max:255')]
    public string $houseNo;

    #[Rule('required|string|max:255')]
    public string $street;

    public function save()
    {
        $this->validate();
        
        try {
            DB::transaction(function () {
                $districtId = Barangay::firstWhere('id', $this->barangayId)->district_id;

                $beneficiary = Beneficiary::create([
                    'created_by' => Auth::id(),
                    'first_name' => $this->firstName,
                    'middle_name' => $this->middleName,
                    'last_name' => $this->lastName,
                    'suffix' => $this->suffix,
                    'date_of_birth' => $this->dateOfBirth,
                    'date_of_death' => $this->dateOfDeath,
                    'lethal' => $this->lethal ?? false,
                    'pwd' => $this->pwd ?? false,
                    'sex_id' => $this->sexId,
                    'religion_id' => $this->religionId,
                    'barangay_id' => $this->barangayId,
                    'district_id' => $districtId,
                    'city' => 'Taguig City',
                    'house_no' => $this->houseNo,
                    'street' => $this->street,
                ]);

                session()->put('beneficiary_uuid', $beneficiary->uuid);
        
                $this->reset();
        
                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Beneficiary created successfully',
                ]);
            });
        } catch (\Throwable $th) {
            if (app()->hasDebugModeEnabled()) {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'title' => 'Error Occured',
                    'text' => $th->getMessage(),
                ]);
            } else {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'title' => 'Failed to Submit',
                    'text' => 'Something went wrong. Please try again.',
                ]);
            }
        }
    }

    public function render()
    {
        return view('livewire.beneficiary.create');
    }
}
