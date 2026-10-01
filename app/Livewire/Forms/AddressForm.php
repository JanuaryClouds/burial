<?php

namespace App\Livewire\Forms;

use Illuminate\Database\Eloquent\Model;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AddressForm extends Form
{
    #[Validate('required|string')]
    public ?string $regionCode = null;

    #[Validate('nullable|string')]
    public ?string $regionCode_display = null;

    #[Validate('nullable|required_without:municipalityCode|string')]
    public ?string $provinceCode = null;

    #[Validate('nullable|required_without:municipalityCode|string')]
    public ?string $provinceCode_display = null;

    #[Validate('nullable|required_without:provinceCode|string')]
    public ?string $municipalityCode = null;

    #[Validate('nullable|required_without:provinceCode|string')]
    public ?string $municipalityCode_display = null;

    #[Validate('required|string')]
    public ?string $barangayCode = null;

    #[Validate('nullable|string')]
    public ?string $barangayCode_display = null;

    #[Validate('required|string|max:255')]
    public ?string $street = null;

    #[Validate('required|string|max:255')]
    public ?string $houseNumber = null;

    public function setAddress(Model $model): void
    {
        $this->regionCode = $model->address->region_code . ':0:0:0';
        $this->regionCode_display = $model->address->region_name;
        $this->provinceCode = $model->address->region_code . ':' . $model->address->province_code . ':0:0';
        $this->provinceCode_display = $model->address->province_name;
        $this->municipalityCode = $model->address->region_code . ':' . $model->address->province_code . ':' . $model->address->municipality_code . ':0';
        $this->municipalityCode_display = $model->address->municipality_name;
        $this->barangayCode = $model->address->region_code . ':' . $model->address->province_code . ':' . $model->address->municipality_code . ':' . $model->address->barangay_code;
        $this->barangayCode_display = $model->address->barangay_name;
        $this->street = $model->address->street;
        $this->houseNumber = $model->address->house_number;
    }
}
