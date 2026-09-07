<?php

namespace App\Livewire\Interview;

use App\Models\Client;
use App\Models\Interview;
use App\Models\WorkflowStage;
use App\Services\InterviewService;
use App\Services\WorkflowHistoryService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public Client $client;

    private InterviewService $services;

    #[Validate('required|date')]
    public string $schedule;

    public function boot(InterviewService $interviewService)
    {
        $this->services = $interviewService;
    }

    public function mount(Client $client)
    {
        $this->client = $client;
    }

    public function save()
    {
        $this->validate();

        if ($this->client->interviews->where('schedule', '>', now())->count() > 0) {
            return;
        }

        $this->services->store(
            ['schedule' => $this->schedule],
            $this->client->uuid
        );

        $this->reset('schedule');
        $this->dispatch('interviewCreated');
    }

    public function render()
    {
        return view('livewire.interview.create');
    }
}
