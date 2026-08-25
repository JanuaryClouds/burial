<x-card>
	<x-slot:header>Recommendation</x-slot:header>
	@if ($application->assessment)
		@include('recommendation.partials.form', [
			'application' => $application,
		])
	@else
		<span>Client must be assessed first before recommending an assistance.</span>
	@endif
</x-card>
