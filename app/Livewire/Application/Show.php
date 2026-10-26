<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Models\DocumentRequirement;
use App\Services\ApplicationService;
use App\Services\InterviewService;
use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class Show extends Component
{
    public Application $application;

    private ApplicationService $services;

    public string $qrCode;

    public string $barcode;

    public Collection $documents;

    public function mount(Application $application, ApplicationService $applicationServices)
    {
        $this->application = $application;
        $this->services = $applicationServices;

        if ($this->application) {
            $this->qrCode = $this->services->getQrCodeUri(
                'svg',
                $application->qr_code,
                200
            );

            $this->barcode = $this->services->getBarcodeUri($application->qr_code);
        }

        $this->documents = DocumentRequirement::index()->get();
    }

    #[On('refreshWorkflowHistory')]
    #[On('refreshRecommendation')]
    #[On('interviewCreated')]
    #[On('interviewCompleted')]
    public function refreshApplication(): void
    {
        $this->application = Application::findOrFail($this->application->uuid);
    }

    public function refreshInterview(): void
    {
        $this->refreshApplication();
    }

    public function markAsDone(string $uuid): void
    {
        abort_unless($this->application->currentInterviews()->whereKey($uuid)->exists(), 403);
        app(InterviewService::class)->done($uuid);
        $this->refreshApplication();
    }

    public function render()
    {
        $this->application->loadMissing([
            'client.user', 'client.address', 'beneficiary.religion', 'relationship',
            'assessment', 'activeRecommendation.workflow', 'activeRecommendation.funeralAssistanceType',
            'activeRecommendation.modeOfAssistance', 'currentInterviews', 'workflowStage',
            'referral', 'rejection', 'cancellation', 'closure',
            'claimantAssignments.client:uuid,user_id',
        ]);
        $this->authorize('view', $this->application);
        $this->application->loadCount('recommendations');
        $history = $this->application->claimantAssignments->sortBy('sequence');
        if ($history->count() > 1) {
            $history->load(['interviews', 'assignedBy', 'relationship']);
            if (auth()->user()->hasAnyRole(['staff', 'superadmin'])) {
                $history->load(['assessments', 'recommendations.workflow']);
            }
        }
        $latestInterview = $this->application->currentInterviews->sortByDesc('schedule')->first();
        $canCompleteInterview = $latestInterview && auth()->user()->can('complete', [$latestInterview, $this->application]);
        $statusIndicators = StatusTimeline::indicators($this->application->status(), $this->application->closure !== null);

        return view('livewire.application.show', ['claimantHistory' => $history, 'hasClaimantChanges' => $history->count() > 1, 'inlineApplicationDetails' => true, 'statusIndicators' => $statusIndicators, 'latestInterview' => $latestInterview, 'canCompleteInterview' => $canCompleteInterview]);
    }
}
