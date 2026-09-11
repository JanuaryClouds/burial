<?php

namespace App\Livewire\Beneficiary;

use App\Models\Barangay;
use App\Models\Beneficiary;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
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
    public ?bool $lethal = null;

    #[Rule('nullable|boolean')]
    public ?bool $pwd = null;

    #[Rule('required|integer|exists:sexes,id')]
    public int $sexId;

    #[Rule('required|integer|exists:religions,id')]
    public int $religionId;

    #[Rule('required|integer|exists:barangays,id')]
    public int $barangayId;

    // #[Rule('required|integer')]
    // public int $districtId;

    // #[Rule('required|string|max:255')]
    // public string $city;

    #[Rule('required|string|max:255')]
    public string $houseNo;

    #[Rule('required|string|max:255')]
    public string $street;

    public array $family = [];

    public function addFamilyMember()
    {
        if (count($this->family) < 5) {
            $this->family[] = [
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
        unset($this->family[$index]);

        $this->family = array_values($this->family);
    }

    protected function rules(): array
    {
        return [
            'family' => 'array|max:5',
            'family.*.name' => 'required|string|max:255',
            'family.*.dateOfBirth' => 'required|date|before_or_equal:tomorrow',
            'family.*.civilId' => 'required|integer|exists:civil_statuses,id',
            'family.*.sexId' => 'required|integer|exists:sexes,id',
            'family.*.relationshipId' => 'required|integer|exists:relationships,id',
            'family.*.occupation' => 'nullable|string|max:255',
            'family.*.income' => 'nullable|numeric|min:0',
        ];
    }

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

                foreach ($this->family as $member) {
                    $beneficiary->family()->create([
                        'name' => $member['name'],
                        'age' => Carbon::parse($member['dateOfBirth'])->age,
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

                $this->redirect('application.create');
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
