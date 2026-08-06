@props([
    'application' => null,
])
@php
	$documents = App\Models\DocumentRequirement::burial();
@endphp
<div class="row">
	@if (Route::is('application.create'))
		@foreach ($documents as $document)
			@php
				$displayName = $document['name'];
				if ($document['is_muslim']) {
				    $displayName .= ' (For Muslim Citizen Only)';
				}
			@endphp
			<div class="col-12 col-md-6 {{ $document['is_muslim'] ? 'muslim-requirements' : '' }}">
				<x-form.image.input name="images_{{ $document['key'] }}"
					field="images[{{ $document['key'] }}]"
					label="{{ $displayName }}"
					helpText="From {{ $document['source'] }}" />
				<hr>
			</div>
		@endforeach
	@else
		@if ($application)
			@foreach ($documents as $document)
				@php
					$filename = $application->tracking_no . '-' . Str::slug($document['key']) . '.jpeg.enc';

					if (app()->isLocal()) {
					    $filename = 'test-' . $filename;
					}
				@endphp
				<div class="col-12 col-lg-6 mb-4">
					<h6>{{ $document['name'] }}</h6>
					@if (app()->hasDebugModeEnabled())
						<p class="text-muted">Key: {{ $document['key'] }} / Slugged Key:
							{{ Str::slug($document['key']) }} /
							Full filename:
							{{ $filename }}</p>
					@endif
					<img src="{{ route('image', ['filename' => $filename]) }}"
						alt="{{ $document['name'] }}"
						class="img-fluid {{ $document['description'] ? 'for-muslim' : '' }}">
					<hr>
				</div>
			@endforeach
		@else
			<p class="text-muted">No documents found.</p>
		@endif
	@endif
</div>
<script nonce="{{ $nonce ?? '' }}">
	$(document).ready(function() {
		const beneficiaryReligion = $('#beneficiary_religion').find('option:selected').text();

		if (beneficiaryReligion === 'Muslim') {
			$('.muslim-requirements').show();
		} else {
			$('.muslim-requirements').hide();
		}
	});
</script>
