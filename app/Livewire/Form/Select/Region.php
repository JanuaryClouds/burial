<?php

namespace App\Livewire\Form\Select;

use App\Services\PsaClassificationService;
use App\Traits\Livewire\Form\HasSelectProperties;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Region extends Component
{
    use HasSelectProperties;

    public string $name;

    #[Validate('required|string')]
    public ?string $regionId = null;

    public function mount(
        string $name, 
        ?string $selected = null, 
        ?string $label = null, 
        ?string $helpText = null, 
        bool $required = true
    ) {
        $this->name = $name;
        $this->selected = $selected;
        $this->label = $label;
        $this->helpText = $helpText;
        $this->required = $required;
        $this->getOptions();
    }

    public function getOptions()
    {
        $psaService = app(PsaClassificationService::class);

        $this->options = collect($psaService->getRegions())
            ->mapWithKeys(function ($item) {
                return [
                    $item['reg'] => $item['area_name'],
                ];
            })->toArray();
    }

    public function selectedRegion()
    {
        $this->dispatch('selectedRegion', regionId: $this->selected);
    }

    public function render()
    {
        return view('livewire.form.select.region');
    }
}
