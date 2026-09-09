<x-card>
	<x-slot:header>Assessment</x-slot:header>
	@can('view', [\App\Models\Assessment::class])
		@if ($application->assessment)
			<livewire:assessment.show :assessment="$application->assessment"
				defer />
		@else
			@if ($application->finishedInterview())
				@can('create', [\App\Models\Assessment::class, $application])
					<x-alert>
						<x-slot:icon>
							<x-icon.font-awesome class="fa-box-open fs-2" />
						</x-slot:icon>
						No assessment found
						<x-slot:options>
							<a href="{{ route('application.assessment.create', $application) }}"
								class="btn btn-sm btn-primary">
								<x-icon.font-awesome class="fa-pen-to-square" />
								Make an Assessment
							</a>
						</x-slot:options>
					</x-alert>
				@else
					<x-alert>
						<x-slot:icon>
							<x-icon.font-awesome class="fa-lock fs-2" />
						</x-slot:icon>
						You do not have the permission to make an assessment
					</x-alert>
				@endcan
			@else
				<x-alert>
					<x-slot:icon>
						<x-icon.font-awesome class="fa-exclamation-circle fs-2" />
					</x-slot:icon>
					Set an interview first before making an assessment.
					@can('create', [\App\Models\Interview::class, $application->client])
						<x-slot:options>
							<a href="{{ route('client.show', $application->client) }}"
								class="btn btn-sm btn-primary">
								<x-icon.font-awesome class="fa-up-right-from-square" />
								Show Client
							</a>
						</x-slot:options>
					@endcan
				</x-alert>
			@endif
		@endif
	@else
		<x-alert>
			<x-slot:icon>
				<x-icon.font-awesome class="fa-lock fs-2" />
			</x-slot:icon>
			You do not have the permission to view the assessment
		</x-alert>
	@endcan
</x-card>
