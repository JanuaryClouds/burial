<?php

namespace App\Livewire\Forms;

use App\Models\Assessment;
use Livewire\Attributes\Validate;
use Livewire\Form;

class AssessmentForm extends Form
{
    #[Validate('required|string|max:65535')]
    public string $problem_presented;

    #[Validate('required|string|max:65535')]
    public string $swa;

    public function setAssessment(Assessment $assessment): void
    {
        $this->problem_presented = $assessment->problem_presented;
        $this->swa = $assessment->swa;
    }
}
