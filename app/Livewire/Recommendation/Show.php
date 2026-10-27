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
        $application = $this->recommendation->application()->firstOrFail();
        abort_unless(auth()->user()?->isInternal() && auth()->user()->can('view', $application), 403);
        abort_unless(auth()->user()->hasOfficeAccess() || $application->current_recommendation_uuid === $this->recommendation->uuid, 403);

        return view('livewire.recommendation.show');
    }
}
