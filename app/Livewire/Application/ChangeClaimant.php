<?php

namespace App\Livewire\Application;

use App\Models\Application;
use App\Models\Client;
use App\Models\Relationship;
use App\Models\SystemSetting;
use App\Services\ClaimantReplacementService;
use Livewire\Attributes\Locked;
use Livewire\Component;

class ChangeClaimant extends Component
{
    public Application $application;

    #[Locked]
    public string $expectedAssignmentUuid;

    public string $search = '';

    public string $clientUuid = '';

    public ?int $relationshipId = null;

    public string $reason = '';

    public function mount(Application $application): void
    {
        $this->authorize('changeClaimant', $application);
        $this->application = $application;
        $this->expectedAssignmentUuid = $application->current_claimant_assignment_uuid;
    }

    public function save(): void
    {
        $this->application->refresh();
        $this->authorize('changeClaimant', $this->application);
        app(ClaimantReplacementService::class)->replace($this->application->uuid, [
            'client_uuid' => $this->clientUuid,
            'relationship_id' => $this->relationshipId,
            'reason' => $this->reason,
            'expected_assignment_uuid' => $this->expectedAssignmentUuid,
        ]);
        $this->redirect(route('application.show', $this->application));
    }

    public function render()
    {
        $this->application->loadMissing('client.user');
        $this->authorize('changeClaimant', $this->application);
        // Names are encrypted. Search after decryption, rather than SQL LIKE on ciphertext.
        $candidates = trim($this->search) === '' ? collect() : Client::unused()->where('user_id', '!=', $this->application->client->user_id)
            ->whereHas('user')->with('user')->orderByDesc('created_at')->lazy(100)
            ->filter(fn ($client) => str_contains(
                mb_strtolower($client->fullname().' '.$client->user->email.' '.$client->user_id), mb_strtolower(trim($this->search))
            ))->take(20)->collect();
        $selected = $this->clientUuid ? Client::with('user')->find($this->clientUuid) : null;

        return view('livewire.application.change-claimant', [
            'candidates' => $candidates->take(20),
            'selected' => $selected,
            'relationships' => Relationship::orderBy('name')->pluck('name', 'id'),
            'remaining' => max(0, (SystemSetting::first()?->claimant_change_limit ?? 1) - ($this->application->claimantAssignments()->count() - 1)),
        ]);
    }
}
