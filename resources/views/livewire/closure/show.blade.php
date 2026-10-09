<div>
	<x-callout class="bg-info-subtle border-info text-info">
		<x-slot:icon>
			<x-icon.font-awesome class="fa-info-circle text-info" />
		</x-slot:icon>
		<x-slot:title>
			<p class="fw-bold">Application has been closed</p>
		</x-slot:title>
		<p class="text-info">
			{{ $application->closure->reason }}
		</p>
		<p class="text-muted">
			Closed at {{ \Carbon\Carbon::parse($application->closure->closed_at)->format('F d Y, h:i s a') }} by
			{{ $application->closure->closedBy->fullname() }}
		</p>
	</x-callout>
</div>
