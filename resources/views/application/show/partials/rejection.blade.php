@if (!$application->rejection && !$application->referral && !$application->cancellation)
	@if ($application->assessment)
		@can('create', [\App\Models\Rejection::class])
			<x-card>
				<x-slot:header>Rejection</x-slot:header>
				<div class="d-flex flex-column gap-4">
					<x-callout class="bg-danger-subtle border-danger text-danger">
						<x-slot:icon>
							<x-icon.font-awesome class="fa-exclamation-triangle text-danger" />
						</x-slot:icon>
						<x-slot:title>Important</x-slot:title>
						In cases where the client submitted an application for assistance that is not under the Taguig City Local
						Government
						Unit's scope of services, or the client disqualifies, submit a rejection for this application. After submitting a
						rejection, it will notify the client. This action cannot be undone after submission.
					</x-callout>
					<livewire:rejection.create :application="$application"
						defer />
				</div>
			</x-card>
		@else
			<x-card.unauthorized>
			</x-card.unauthorized>
		@endcan
	@else
		<x-card>
			<x-slot:header>Rejection</x-slot:header>
			<x-alert>
				<x-slot:icon>
					<x-icon.font-awesome class="fa-exclamation-circle fs-2" />
				</x-slot:icon>
				@if (!$application->finishedInterview())
					Set an interview first before rejecting the application.
					@can('create', [\App\Models\Interview::class, $application->client])
						<x-slot:options>
							<a href="{{ route('client.show', $application->client) }}"
								class="btn btn-sm btn-primary">
								<x-icon.font-awesome class="fa-up-right-from-square" />
								Show Client
							</a>
						</x-slot:options>
					@endcan
				@elseif (!$application->assessment)
					Make an assessment first before making rejecting the application
					@can('create', [\App\Models\Assessment::class, $application])
						<x-slot:options>
							<a href="{{ route('application.assessment.create', $application) }}"
								class="btn btn-sm btn-primary">
								<x-icon.font-awesome class="fa-pen-to-square" />
								Make an Assessment
							</a>
						</x-slot:options>
					@endcan
				@endif
			</x-alert>
		</x-card>
	@endif
@endif
