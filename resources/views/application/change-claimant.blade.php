@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-6">
        <x-card>
            <x-slot:header>Application Summary</x-slot:header>
            <livewire:application.summary :application="$application" />
            <x-slot:footer>
                <a href="{{ route('application.show', $application) }}" class="btn btn-sm btn-light">Back to Application</a>
            </x-slot:footer>
        </x-card>
        <x-card>
            <x-slot:header>Change Claimant</x-slot:header>
            <livewire:application.change-claimant :application="$application" />
        </x-card>
    </div>
@endsection
