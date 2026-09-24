<div class="d-flex flex-column gap-2">
	<x-callout class="bg-info-subtle border-info text-info">
		<x-slot:icon>
			<x-icon.font-awesome class="fs-2 fa-forward text-info" />
		</x-slot:icon>
		<x-slot:title>
			Application has been cancelled
		</x-slot:title>
		<p class="mb-0">{{ $cancellation->reason }}</p>
		<p class="text-muted small mb-0">{{ \Carbon\Carbon::parse($cancellation->created_at)->format('d F Y h:i A') }}</p>
	</x-callout>
</div>
