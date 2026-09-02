<?php

namespace App\Livewire\Recommendation;

use App\Models\Recommendation;
use Livewire\Component;

class Show extends Component
{
    public Recommendation $recommendation;

    public function mount(Recommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    public function render()
    {
        return view('livewire.recommendation.show');
    }
}
