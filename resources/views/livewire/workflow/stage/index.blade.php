<div class="row gy-4">
    @foreach ($stages as $stage)
        <div class="col-12 col-lg-6" wire:key="saved-stage-{{ $stage->uuid }}">
            <x-card>
                <x-slot:header>Stage {{ $stage->position }}: {{ $stage->name }}</x-slot:header>
                <p>{{ $stage->description }}</p>
                <p class="text-muted">Permission: {{ $stage->permission?->name ?? 'Unassigned' }}</p>
            </x-card>
        </div>
    @endforeach
</div>