@props([
    'client' => null,
])

@php
    $documents = App\Models\DocumentRequirement::burial();
@endphp
<h5 class="card-title">SUBMITTED DOCUMENTS</h5>
@if (Request::routeIs('general.intake.form'))
    <p><strong>Optionally</strong>, you can upload the following documents to shorten the processing time during the
        interview:</p>
@endif
<div class="row flex-column justify-content-center align-items-center g-2">
    @if (Route::is('general.intake.form'))
        @foreach ($documents as $document)
            @php
                $displayName = $document['name'];
                if ($document['is_muslim']) {
                    $displayName .= ' (For Muslim Citizen Only)';
                }
            @endphp
            <div class="col mb-4 {{ $document['is_muslim'] ? 'muslim-requirements' : '' }}">
                <x-form-image-submission name="images[{{ $document['key'] }}]" label="{{ $displayName }}"
                    helpText="From {{ $document['source'] }}" />
                <hr>
            </div>
        @endforeach
    @else
        @if ($client)
            @foreach ($documents as $document)
                @php
                    $filename = $client->tracking_no . '-' . Str::slug($document['key']) . '.jpeg.enc';

                    if (app()->isLocal()) {
                        $filename = 'test-' . $filename;
                    }

                    if (app()->hasDebugModeEnabled()) {
                        $filename = 'test.png';
                    }
                @endphp
                <div class="col mb-4">
                    <h6>{{ $document['name'] }}</h6>
                    @if (app()->hasDebugModeEnabled())
                        <p class="text-muted">Key: {{ $document['key'] }} / Slugged Key:
                            {{ Str::slug($document['key']) }} /
                            Full filename:
                            {{ $filename }}</p>
                    @endif
                    <img src="{{ route('image', ['filename' => $filename]) }}" alt="{{ $document['name'] }}"
                        class="img-fluid">
                    <hr>
                </div>
            @endforeach
        @else
            <p class="text-muted">No documents found.</p>
        @endif
    @endif
</div>
