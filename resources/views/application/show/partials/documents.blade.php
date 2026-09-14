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
					<h5 class="">
						{{ Str::title(Str::replace('_', ' ', $document['key'])) }}
					</h5>
					<x-image.viewer src="{{ route('image', $document['key']) }}"
						:alt="$document['name']"
						defer />
				</div>
				<div class="separator separator-dashed my-4"></div>
			</div>
		@endforeach
	</div>
</div>
