<?php

namespace App\Livewire\Forms;

use App\Models\Recommendation;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RecommendationForm extends Form
{
    #[Validate('required|exists:funeral_assistance_types,uuid')]
    public ?string $funeralAssistanceTypeUuid = null;

    #[Validate('required|numeric|min:0')]
    public ?int $amountExtended = null;

    #[Validate('required|exists:mode_of_assistances,id')]
    public ?int $modeOfAssistanceId = null;

    public function setRecommendation(Recommendation $recommendation)
    {
        $this->funeralAssistanceTypeUuid = $recommendation->funeral_assistance_type_uuid;
        $this->amountExtended = $recommendation->amount_extended;
        $this->modeOfAssistanceId = $recommendation->mode_of_assistance_id;
    }
}
