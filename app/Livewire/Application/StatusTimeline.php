<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Traits\Livewire\HasPlaceholder;
use Livewire\Attributes\On;
use Livewire\Component;

class StatusTimeline extends Component
{
    use HasPlaceholder;

    public Application $application;

    public array $status;

    public array $statusIndicators;

    public function mount(Application $application)
    {
        $application->loadMissing(['client.user', 'currentInterviews', 'referral', 'rejection', 'cancellation', 'closure', 'workflowStage']);
        $this->application = $application;
        $this->status = $application->status();

        $this->statusIndicators = self::indicators($this->status, $application->closure !== null);
    }

    public static function indicators(array $status, bool $closed): array
    {
        $indicators = [
            ['pending' => 'current'],
            ['assessment' => false],
            ['processing' => false],
            ['releasing' => false],
        ];

        for ($i = 0; $i < count($indicators); $i++) {
            $currentLabel = key($indicators[$i]);
            if (collect($status)->pluck('label')->contains($currentLabel)) {
                $indicators[$i][$currentLabel] = 'current';

                if ($i !== 0) {
                    $previousLabel = key($indicators[$i - 1]);
                    $indicators[$i - 1][$previousLabel] = 'completed';
                }

                if ($closed) {
                    $indicators[$i][$currentLabel] = false;
                }
            }
        }

        foreach ($indicators as $key => $value) {
            if (is_array($value)) {
                $label = key($value);
                $indicators[$label] = $value[$label];
                unset($indicators[$key]);
            }
        }

        return $indicators;
    }

    #[On('refreshWorkflowHistory')]
    #[On('interviewCompleted')]
    public function refresh()
    {
        $this->application->refresh();
        $this->mount($this->application);
    }

    public function render()
    {
        return view('livewire.application.status-timeline');
    }
}
