<?php

namespace App\Livewire\Recommendation;

use App\Models\Recommendation;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    use HasPlaceholder;

    public Recommendation $recommendation;

    public function mount(Recommendation $recommendation)
    {
        $this->recommendation = $recommendation;
    }

    #[On('refreshRecommendation')]
    public function refresh()
    {
        $this->recommendation->refresh();
    }

    public function render()
    {
        return view('livewire.recommendation.show');
    }
}
