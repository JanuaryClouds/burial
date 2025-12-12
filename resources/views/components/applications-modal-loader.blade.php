@props([
    'modalsLoaded' => false,
    'applications' => [],
    'application_id' => null,
])
@if (!$modalsLoaded)
    @if (isset($application_id))
        <x-reject-modal :application="$application" />
        <x-process-updater :application="$application" />
    @elseif (isset($applications))
        @foreach ($applications as $application)
            <x-reject-modal :application="$application" />
            <x-process-updater :application="$application" />
        @endforeach
    @endif

    @php $modalsLoaded = true; @endphp
@endif
