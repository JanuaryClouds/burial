@can('create', [\App\Models\WorkflowHistory::class, $application])
	<livewire:workflow.history.create :application="$application" />
@else
	<x-card.unauthorized>
		<x-icon.font-awesome class="fa-lock fs-4" />
		<p class="fs-5 fw-semibold">
			@if ($application->referral)
				Application has been referred
			@endif
			@if ($application->cancellation)
				Application has been cancelled
			@endif
		</p>
	</x-card.unauthorized>
@endcan
