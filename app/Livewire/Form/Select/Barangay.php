<?php

namespace App\Livewire\Form\Select;

use App\Services\PsaClassificationService;
use App\Traits\Livewire\Form\HasSelectProperties;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Barangay extends Component
{
    use HasSelectProperties;

    public string $name;

    public ?string $provinceId = null;

    #[Validate('required|string')]
    public ?string $barangayId = null;

    public function mount(
        string $name,
        string $label = 'Barangay',
        ?string $provinceId = null,
        bool $required = true,
        array $options = [],
        ?string $selected = null,
        string $helpText = '',
    ) {
        $this->provinceId = $provinceId;
        $this->name = $name;
        $this->label = $label;
        $this->required = $required;
        $this->options = $options;
        $this->selected = $selected;
        $this->helpText = $helpText;
    }

    public function getOptions()
    {
        $psaServices = app(PsaClassificationService::class);

        $this->options = collect($psaServices->getBarangays($this->provinceId))
            ->mapWithKeys(function ($item) {
                return [
                    $item['bgy'] => $item['area_name'],
                ];
            })
            ->toArray();
    }

    public function render()
    {
        return view('livewire.form.select.barangay');
    }
}
