<?php

namespace App\Livewire\Interview;

use App\Models\Client;
use App\Models\Interview;
use App\Models\InterviewSchedulingRequest;
use App\Services\InterviewSchedulingService;
use App\Services\InterviewService;
use App\Traits\Livewire\GuardsOfficeForm;
use Illuminate\Database\QueryException;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Create extends Component
{
    use GuardsOfficeForm;

    public function bootOfficeAccess(): void
    {
        abort_unless(auth()->user()?->canOfficeAction('Create Interview'), 403);
    }

    public Client $client;

    #[Locked]
    public ?string $expectedAssignmentUuid = null;

    private InterviewService $services;

    #[Validate('required|date|after_or_equal:now')]
    public string $schedule;

    public bool $scheduled;

    #[Locked]
    public ?string $pendingRequestUuid = null;

    public bool $confirmUncertain = false;

    public function boot(InterviewService $interviewService): void
    {
        $this->services = $interviewService;
    }

    public function mount(Client $client): void
    {
        $this->client = $client;
        $this->expectedAssignmentUuid = $client->application()->value('current_claimant_assignment_uuid');
        $pending = app(InterviewSchedulingService::class)->latest($client->uuid, $this->expectedAssignmentUuid);
        if ($pending) {
            $this->pendingRequestUuid = $pending->uuid;
            $this->schedule = $pending->schedule->format('Y-m-d\TH:i');
        }

        $this->scheduled = $this->client->interviews()
            ->where('claimant_assignment_uuid', $this->expectedAssignmentUuid)
            ->where('status', 'scheduled')
            ->exists();
    }

    public function save(): void
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
            $this->services->store(['schedule' => $this->schedule], $this->client->uuid, $this->expectedAssignmentUuid);
            $this->success();
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
            $this->pendingRequestUuid = app(InterviewSchedulingService::class)->latest($this->client->uuid, $this->expectedAssignmentUuid)?->uuid;
        } catch (QueryException) {
            $this->pendingRequestUuid = app(InterviewSchedulingService::class)->latest($this->client->uuid, $this->expectedAssignmentUuid)?->uuid;
            $this->addError('schedule', 'Scheduling could not finish. Saved notification outcomes are preserved; retry without changing the slot.');
        }
    }

    public function retry(): void
    {
        abort_unless($this->pendingRequestUuid !== null, 404);
        $request = InterviewSchedulingRequest::findOrFail($this->pendingRequestUuid);
        abort_unless($request->client_uuid === $this->client->uuid && $request->claimant_assignment_uuid === $this->expectedAssignmentUuid, 403);
        if (Carbon::parse($this->schedule)->format('Y-m-d H:i:s') !== $request->schedule->format('Y-m-d H:i:s')) {
            $this->addError('schedule', 'The slot changed. Use Schedule Interview to start a new request; accepted messages cannot be recalled.');

            return;
        }
        try {
            app(InterviewSchedulingService::class)->resume($request->uuid, $this->confirmUncertain);
            $this->success();
        } catch (ValidationException $exception) {
            $this->setErrorBag($exception->validator->errors());
        } catch (QueryException) {
            $this->addError('schedule', 'Scheduling could not be finalized. Saved notification outcomes are preserved; retry without changing the slot.');
        }
    }

    private function success(): void
    {
        $this->pendingRequestUuid = null;
        $this->confirmUncertain = false;
        $this->reset('schedule');
        $this->scheduled = true;
        $this->dispatch('interviewCreated');
        $this->dispatch('notification:alert', ['type' => 'success', 'text' => 'Interview scheduled successfully']);
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
