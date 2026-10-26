<?php

namespace App\Livewire\Interview;

use App\Models\Client;
use App\Models\Interview;
use App\Services\InterviewService;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Component;

class Index extends Component
{
    public Client $client;

    public Collection $interviews;

    #[Locked]
    public ?string $assignmentUuid = null;

    public function mount(Client $client, ?string $assignmentUuid = null)
    {
        $this->client = $client;
        $this->assignmentUuid = $assignmentUuid;
        $this->refresh();
    }

    #[On('interviewCreated')]
    public function refresh()
    {
        $this->interviews = $this->client->interviews()->with(['client', 'claimantAssignment'])
            ->when($this->assignmentUuid, fn ($q) => $q->where('claimant_assignment_uuid', $this->assignmentUuid))
            ->orderByDesc('schedule')->limit(1)->get();
    }

    public function markAsDone(Interview $interview)
    {
        abort_if($this->assignmentUuid && $interview->claimant_assignment_uuid !== $this->assignmentUuid, 403);
        abort_unless($interview->client_uuid === $this->client->uuid, 403);
        app(InterviewService::class)->done($interview->uuid);
        $this->dispatch('interviewCompleted');

        $this->refresh();
    }

    public function render()
    {
        $latest = $this->interviews->first();

        return view('livewire.interview.index', [
            'latestInterview' => $latest,
            'canComplete' => $latest && auth()->user()->can('complete', $latest),
        ]);
    }
}
