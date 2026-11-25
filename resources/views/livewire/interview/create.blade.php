<div data-status-loading="save" class="d-flex flex-column gap-3 mb-5">
    @if ($completed)
        <p class="text-muted mb-0">The interview for this claimant is complete.</p>
    @elseif ($scheduled)
        <p class="text-muted mb-0">An interview is already scheduled for this claimant.</p>
    @elseif ($canSchedule)
        <x-form.input name="schedule" label="Interview schedule" type="datetime-local" wire:model="schedule" />
        @error('schedule') <p class="text-danger">{{ $message }}</p> @enderror
        @if ($pendingRequestUuid)
            <div class="alert alert-warning">
                The interview is not yet scheduled. Accepted notifications will not be resent. Changing the slot cannot recall messages already accepted.
                <label class="d-block"><input type="checkbox" wire:model="confirmUncertain"> I understand that retrying uncertain delivery may send a duplicate message.</label>
                <button type="button" class="btn btn-warning btn-sm" wire:click="retry" wire:loading.attr="disabled">Retry missing notifications</button>
            </div>
        @endif
        <div class="d-flex justify-content-end">
            <x-button type="button" wire:click="save" wire:loading.attr="disabled" class="btn btn-sm btn-primary">
                <i class="fa-solid fa-calendar" wire:loading.remove></i>
                <span wire:loading>Scheduling...</span>
                <span wire:loading.remove>Schedule Interview</span>
            </x-button>
        </div>
    @else
        <p class="text-muted mb-0">Interview scheduling is unavailable for this application.</p>
    @endif
</div>
