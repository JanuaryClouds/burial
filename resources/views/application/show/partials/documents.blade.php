@php
	$documents = App\Models\DocumentRequirement::burial();
@endphp
<div class="d-flex flex-column gap-4">
	<div class="row">
		@foreach ($documents as $document)
			@if ($application->beneficiary->religion->name != 'Muslim' && $document['is_muslim'])
				@continue
			@endif
			<div class="col-12 col-lg-6 p-4">
				<div class="d-flex flex-column gap-4">
					<x-image.viewer src="{{ route('application.image', [$application, $document['key']]) }}"
						:applicationUuid="$application->uuid"
						:alt="$document['name']" />
				</div>
			</div>
		@endforeach
	</div>
</div>
