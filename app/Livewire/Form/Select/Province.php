<?php

namespace App\Livewire\Form\Select;

use App\Services\PsaClassificationService;
use App\Traits\Livewire\Form\HasSelectProperties;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Province extends Component
{
    use HasSelectProperties;

    public string $name;

    public ?string $regionId = null;

    #[Validate('required|string')]
    public ?string $provinceId = null;

    public function mount(
        string $name, 
        ?string $regionId = null,
        string $label = 'Province/City', 
        bool $required = true, 
        array $options = [], 
        $selected = null 
    ) {
        $this->regionId = $regionId;
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->options = $options;
        $this->selected = $selected;
    }

    public function getOptions()
    {
        $psaServices = app(PsaClassificationService::class);

        $this->options = $psaServices->getProvinces($this->regionId)
            ->mapWithKeys(function ($item) {
                return [
                    $item['prv'] => $item['area_name'],
                ];
            })
            ->toArray();
    }

    public function selectedProvince()
    {
        $this->dispatch('selectedProvince', provinceId: $this->selected);
    }

    public function render()
    {
        return view('livewire.form.select.province');
    }
}
