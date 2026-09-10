<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Services\ApplicationService;
use Livewire\Attributes\On;
use Livewire\Component;

class StatusTimeline extends Component
{
    public Application $application;

    public array $status;

    public array $statusIndicators;

    public function mount(Application $application)
    {
        $this->application = $application;
        $this->status = $application->status();

        $this->statusIndicators = [
		    ['pending' => 'current'],
		    ['assessment' => false],
		    ['processing' => false],
		    ['releasing' => false],
		    ['closed' => false],
		];

		for ($i = 0; $i < count($this->statusIndicators); $i++) {
		    $currentLabel = key($this->statusIndicators[$i]);
		    if (collect($this->status)->pluck('label')->contains($currentLabel)) {
		        $this->statusIndicators[$i][$currentLabel] = 'current';

		        if (
		            $this->application->referral ||
		            ($this->application->recommendations->count() > 0 &&
		                $this->application->currentRecommendation()->status == 'cancelled')
		        ) {
		            $this->statusIndicators[$i][$currentLabel] = 'completed';
		        }

		        if ($i !== 0) {
		            $previousLabel = key($this->statusIndicators[$i - 1]);
		            $this->statusIndicators[$i - 1][$previousLabel] = 'completed';
		        }

                if ($i === count($this->statusIndicators) - 1) {
                    $this->statusIndicators[$i][$currentLabel] = 'completed';
                }
		    }
		}

		foreach ($this->statusIndicators as $key => $value) {
		    if (is_array($value)) {
		        $label = key($value);
		        $this->statusIndicators[$label] = $value[$label];
		        unset($this->statusIndicators[$key]);
		    }
		}
    }

    #[On('refreshWorkflowHistory')]
    public function refresh()
    {
        $this->status = $this->application->status();
    }

    public function placeholder()
    {
        return view('card.loading');
    }

    public function render()
    {
        return view('livewire.application.status-timeline');
    }
}
