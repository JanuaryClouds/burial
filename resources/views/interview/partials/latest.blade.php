<div class="d-flex flex-column gap-2">
    <h5 class="mb-1">Current claimant interview</h5>
    @if ($latestInterview)
        <span><strong>Latest schedule:</strong> {{ \Carbon\Carbon::parse($latestInterview->schedule)->format('F d, Y g:i A') }}</span>
        <span><strong>Status:</strong> {{ ucfirst($latestInterview->status) }}</span>
        @if (filled($latestInterview->getAttributes()['remarks'] ?? null))
            <p class="mb-0"><strong>Remarks:</strong> {{ $latestInterview->getAttributes()['remarks'] }}</p>
        @endif
        @if ($canComplete)
            <div><button type="button" class="btn btn-sm btn-success mt-2" wire:click="markAsDone('{{ $latestInterview->uuid }}')" wire:loading.attr="disabled">Mark As Done</button></div>
        @endif
    @else
        <p class="text-muted mb-0">No interview scheduled for the current claimant.</p>
    @endif
</div>
