@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-6">
        <x-card>
            <x-slot:header>{{ $pageTitle }}</x-slot:header>
            <p class="text-muted">Read-only inspection · Checked at {{ $inspectedAt->format('F j, Y g:i:s A T') }}. Reload this page to inspect again.</p>
            <x-callout class="bg-info-subtle border-info">
                <x-slot:title>Environment setup information</x-slot:title>
                <p>Configuration completeness does not prove connectivity or delivery. Database and provider connectivity are not checked.</p>
                <p class="mb-0">Update configuration in .env through the deployment process. When configuration is cached, CI/CD must refresh it. PHP, dependency, and filesystem issues require deployment or runtime corrections. There are no repair actions on these pages.</p>
            </x-callout>
        </x-card>
        <div class="row g-4">
            @foreach ($rows as $row)
                <div class="col-12 col-lg-6">
                    <x-card class="h-100">
                        <x-slot:header>{{ $row['label'] }}</x-slot:header>
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @isset($row['mode'])
                                <span class="badge bg-secondary text-white">{{ $row['mode'] }}</span>
                            @endisset
                            <span @class(['badge', 'bg-success-subtle text-success' => in_array($row['status'], ['Passed', 'Configuration complete']), 'bg-danger-subtle text-danger' => in_array($row['status'], ['Failed', 'Invalid configuration', 'Missing configuration']), 'bg-secondary text-white' => ! in_array($row['status'], ['Passed', 'Configuration complete', 'Failed', 'Invalid configuration', 'Missing configuration'])])>{{ $row['status'] }}</span>
                        </div>
                        <p>{{ $row['detail'] }}</p>
                        @if (! empty($row['variables']))
                            <p class="small text-muted mb-0 text-break">Environment settings: {{ $row['variables'] }}</p>
                        @endif
                    </x-card>
                </div>
            @endforeach
        </div>
        @if ($packages !== null)
            <x-card>
                <x-slot:header>Software Versions</x-slot:header>
                <details>
                    <summary class="fw-bold">Direct runtime Composer dependencies</summary>
                    <p class="text-muted mt-3">Installed versions only. This is not an update or vulnerability check.</p>
                    @forelse ($packages as $package)
                        <div class="d-flex flex-wrap justify-content-between gap-2 py-2 border-bottom">
                            <span class="text-break">{{ $package['name'] }}</span><span>{{ $package['version'] }}</span>
                        </div>
                    @empty
                        <p>Dependency metadata unavailable.</p>
                    @endforelse
                </details>
            </x-card>
        @endif
    </div>
@endsection
