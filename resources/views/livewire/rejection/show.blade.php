<div class="d-flex flex-column gap-2">
	<x-callout class="bg-info-subtle border-info text-info">
		<x-slot:icon>
			<x-icon.font-awesome class="fs-2 fa-stop text-info" />
		</x-slot:icon>
		<x-slot:title>
			Application Rejected
		</x-slot:title>
		<p class="mb-0">{{ $rejection->reason }}</p>
		<p class="mb-0 text-muted small">{{ \Carbon\Carbon::parse($rejection->created_at)->format('d F Y h:i A') }}</p>
	</x-callout>
</div>
