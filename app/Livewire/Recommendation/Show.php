<?php

namespace App\Livewire\Recommendation;

use App\Models\Recommendation;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public Recommendation $recommendation;

    public function mount(Recommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    #[On('refreshRecommendation')]
    public function refresh()
    {
        $this->recommendation = $this->recommendation->application->currentRecommendation();
    }

    public function placeholder()
    {
        return view('components.card.loading');
    }

    public function render()
    {
        return view('livewire.recommendation.show');
    }
}
