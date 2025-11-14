@props([
    'modalsLoaded' => false,
    'applications' => [],
    'application_id' => null,
])
@if (! $modalsLoaded)
    @if ($application_id)
        @foreach ($applications as $application)
            @if ($application->id == $application_id)
                <x-reject-modal :application="$application" />
                <x-process-updater :application="$application"/>
                @break
            @endif
        @endforeach
    @else
        @if (count($applications) > 0)
            @foreach ($applications as $application)
                <x-reject-modal :application="$application" />
                <x-process-updater :application="$application"/>
            @endforeach
        @endif
    @endif

    @php $modalsLoaded = true; @endphp
@endif