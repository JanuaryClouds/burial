<x-card>
    <x-slot:header>Stage {{ $stage->position }}: {{ $stage->name }}</x-slot:header>
    <p>{{ $stage->description }}</p>
    <p class="text-muted">Permission: {{ $stage->permission?->name ?? 'Unassigned' }}</p>
</x-card>