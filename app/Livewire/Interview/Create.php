<?php

namespace App\Livewire\Interview;

use App\Models\Client;
use App\Models\Interview;
use App\Models\WorkflowStage;
use App\Services\InterviewService;
use App\Services\WorkflowHistoryService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public Client $client;

    private InterviewService $services;

    #[Validate('required|date|after_or_equal:now')]
    public string $schedule;

    public bool $scheduled;

    public function boot(InterviewService $interviewService)
    {
        $this->services = $interviewService;
    }

    public function mount(Client $client)
    {
        $this->client = $client;

        $this->scheduled = $this->client->interviews()
            ->where('schedule', '>', now())
            ->where('status', 'scheduled')
            ->exists();
    }

    public function save()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->dispatch('notification:toast', [
                'type' => 'error',
                'title' => 'Invalid Form Submitted',
                'text' => 'Please check your inputs and try again.',
            ]);

            report($e);
            return;
        }

        try {
            DB::transaction(function() {
                if ($this->client->interviews->where('schedule', '>', now())->count() > 0) {
                    return;
                }
        
                $this->services->store(
                    ['schedule' => $this->schedule],
                    $this->client->uuid
                );
        
                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Interview scheduled successfully',
                ]);
        
                $this->reset('schedule');
                $this->dispatch('interviewCreated');
            });  
        } catch (\Throwable $th) {
            if (app()->hasDebugModeEnabled()) {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'text' => $th->getMessage(),
                ]);
            } else {
                $this->dispatch('notification:alert', [
                    'type' => 'error',
                    'text' => 'Something went wrong. Try again later.',
                ]);
            }
        }
    }

    #[On('interviewCreated')]
    public function refresh()
    {
        $this->scheduled = $this->client->interviews()
            ->where('schedule', '>', now())
            ->where('status', 'scheduled')
            ->exists();
    }

    public function render()
    {
        return view('livewire.interview.create');
    }
}
