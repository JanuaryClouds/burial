<?php

namespace App\Livewire\Form\Select;

use App\Services\PsaClassificationService;
use App\Traits\Livewire\Form\HasSelectProperties;
use Livewire\Component;

class City extends Component
{
    use HasSelectProperties;

    public string $name;

    public ?string $provinceId = null;

    public function mount(
        string $name,
        ?string $provinceId = null,
        string $label = 'City',
        bool $required = true,
        array $options = [],
        $selected = null
    ) {
        $this->name = $name;
        $this->provinceId = $provinceId;
        $this->label = $label;
        $this->required = $required;
        $this->options = $options;
        $this->selected = $selected;
        $this->getOptions();
    }

    public function getOptions()
    {
        $psaServices = app(PsaClassificationService::class);

        if ($this->provinceId) {
            $this->options = collect($psaServices->getCities($this->provinceId))
                ->mapWithKeys(function ($item) {
                    return [
                        $item['mun'] => $item['area_name'],
                    ];
                })
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.form.select.city');
    }
}
