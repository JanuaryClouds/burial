<div class="d-flex flex-column gap-4" data-document-gallery>
	<div class="row">
		@foreach ($documents as $document)
			@php
				$nameKey = str_replace(' ', '_', strtolower($document['name']));
			@endphp
			@if ($application->beneficiary->religion->name != 'Islam' && $document['for_muslim'])
				@continue
			@endif
			<div class="col-12 col-lg-6 p-4" data-document-slot hidden
				wire:key="application-document-{{ $application->uuid }}-{{ $document['id'] }}" wire:ignore>
				<div class="d-flex flex-column gap-4">
					<x-image.viewer src="{{ route('application.image.get', [$application, $nameKey]) }}"
						:applicationUuid="$application->uuid"
						:alt="$document['name']" />
				</div>
			</div>
		@endforeach
	</div>
	<p class="text-muted mb-0" data-document-empty hidden>No uploaded documents available.</p>
</div>
