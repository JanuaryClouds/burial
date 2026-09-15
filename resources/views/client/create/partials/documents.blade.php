<x-card>
	<x-slot:header>Notice</x-slot:header>
	<div class="d-flex flex-column gap-4">
		Before proceeding into your application please, make sure you have the following documents. Prepare soft-copies
		or photos of the following documents. These are to be submitted when finalizing your application.

		<div class="d-flex flex-column gap-2 border border-2 rounded border-info bg-info-subtle p-4">
			<span class="fw-bold fs-6">Required Documents:</span>
			<ul class="list-unstyled m-0">
				@foreach ($requiredDocuments as $document)
					<li>
						<x-icon.font-awesome class="fa-check" />
						{{ Str::title(Str::replace('_', ' ', $document['name'])) . ($document['is_muslim'] ? ' (For Muslim Beneficiaries)' : '') }}
					</li>
				@endforeach
			</ul>
		</div>
	</div>
</x-card>
