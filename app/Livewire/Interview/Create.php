<?php

namespace App\Livewire\Interview;

use App\Models\Client;
use App\Models\Interview;
use App\Services\ActivityLoggerService;
use App\Services\InterviewService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    public Client $client;

    #[Locked]
    public ?string $expectedAssignmentUuid = null;

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
        $this->expectedAssignmentUuid = $client->application()->value('current_claimant_assignment_uuid');

        $this->scheduled = $this->client->interviews()
            ->where('claimant_assignment_uuid', $this->expectedAssignmentUuid)
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
                'text' => app()->hasDebugModeEnabled() ? $e->getMessage() : config('constants.errors.validation'),
            ]);

            return;
        }

        try {
            DB::transaction(function () {
                $interview = $this->services->store(
                    ['schedule' => $this->schedule],
                    $this->client->uuid,
                    $this->expectedAssignmentUuid
                );

                $this->dispatch('notification:alert', [
                    'type' => 'success',
                    'text' => 'Interview scheduled successfully',
                ]);

                $this->reset('schedule');
                $this->dispatch('interviewCreated');

                ActivityLoggerService::logSuccess('Successfully created an Interview for a client', [
                    'client_uuid' => $this->client->uuid,
                    'interview_uuid' => $interview->uuid,
                    'interview_schedule' => $interview->schedule,
                ]);
            });
        } catch (\Throwable $th) {
            $this->dispatch('notification:alert', [
                'type' => 'error',
                'text' => app()->hasDebugModeEnabled() ? $th->getMessage() : config('constants.errors.unknown'),
            ]);

            ActivityLoggerService::logException($th, 'Unable to create interview');

            report($th);
        }
    }

    #[On('interviewCreated')]
    public function refresh()
    {
        $this->scheduled = $this->client->interviews()
            ->where('claimant_assignment_uuid', $this->expectedAssignmentUuid)
            ->where('status', 'scheduled')
            ->exists();
    }

    public function render()
    {
        return view('livewire.interview.create', [
            'completed' => $this->client->interviews()->where('claimant_assignment_uuid', $this->expectedAssignmentUuid)->where('status', 'done')->exists(),
            'canSchedule' => auth()->user()->can('create', [Interview::class, $this->client]),
        ]);
    }
}
