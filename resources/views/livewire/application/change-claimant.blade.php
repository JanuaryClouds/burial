<div class="border border-gray-300 rounded p-5 mt-3">
    <h4 class="mb-4">Change Claimant</h4>
    <p>Remaining changes: {{ $remaining }}. The replacement must have an existing account and a complete, unused client draft.</p>
    <div class="d-flex flex-column">
        <label class="form-label" for="claimant-search">Search by name, email, or account ID</label>
        <input id="claimant-search" type="search" class="form-control mb-3" wire:model.live.debounce.400ms="search" autocomplete="off">
        <label class="form-label" for="replacement-client">Replacement client draft</label>
        <select id="replacement-client" class="form-select mb-3" wire:model.live="clientUuid" required>
            <option value="">Select a draft</option>
            @if ($selected && ! $candidates->contains('uuid', $selected->uuid))
                <option value="{{ $selected->uuid }}">{{ $selected->fullname() }} — {{ $selected->user?->email }} — {{ $selected->created_at?->format('d M Y H:i') }}</option>
            @endif
            @foreach ($candidates as $candidate)
                <option value="{{ $candidate->uuid }}">{{ $candidate->fullname() }} — {{ $candidate->user->email }} — {{ $candidate->created_at?->format('d M Y H:i') }}</option>
            @endforeach
        </select>
        @error('clientUuid') <p class="text-danger">{{ $message }}</p> @enderror
        @error('client_uuid') <p class="text-danger">{{ $message }}</p> @enderror
        <label class="form-label" for="claimant-relationship">Relationship to beneficiary</label>
        <select id="claimant-relationship" class="form-select mb-3" wire:model.live="relationshipId" required>
            <option value="">Select relationship</option>
            @foreach ($relationships as $id => $name)<option value="{{ $id }}">{{ $name }}</option>@endforeach
        </select>
        @error('relationship_id') <p class="text-danger">{{ $message }}</p> @enderror
        <label class="form-label" for="claimant-reason">Reason for replacement</label>
        <textarea id="claimant-reason" class="form-control mb-3" wire:model.live.debounce.300ms="reason" required maxlength="5000"></textarea>
        @error('reason') <p class="text-danger">{{ $message }}</p> @enderror
        @error('application') <p class="text-danger">{{ $message }}</p> @enderror
        <div class="d-flex justify-content-end">
            <x-modal :modalId="'confirm-claimant-'.$application->uuid"
                modalTitle="Confirm Claimant Change" modalSize="md" buttonClass="btn-sm btn-warning"
                wire:ignore.self>
                <x-slot:triggerButton>Review Claimant Change</x-slot:triggerButton>
                <p>Replace <strong>{{ $application->client->fullname() }}</strong> with <strong>{{ $selected?->fullname() ?? 'the selected claimant' }}</strong>?</p>
                <p>Processing will restart with a new interview, assessment, and recommendation. Existing records and documents will be retained.</p>
                <p><strong>Reason:</strong> {{ $reason }}</p>
                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        @foreach ($errors->all() as $error)<p class="mb-1">{{ $error }}</p>@endforeach
                    </div>
                @endif
                <x-slot:footer>
                    <button type="button" wire:click="save" wire:loading.attr="disabled" class="btn btn-sm btn-warning"
                        @disabled($remaining === 0 || ! $selected || ! $relationshipId || trim($reason) === '')>Confirm Claimant Change</button>
                </x-slot:footer>
            </x-modal>
        </div>
    </div>
</div>
