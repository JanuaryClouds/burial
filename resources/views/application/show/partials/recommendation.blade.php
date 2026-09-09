<x-card>
	<x-slot:header>Recommendation</x-slot:header>
	@can('view', [\App\Models\Recommendation::class])
		@if ($application->recommendations->count() > 0)
			<livewire:recommendation.show :recommendation="$application->currentRecommendation()"
				defer />
			@can('create', [\App\Models\Recommendation::class, $application])
				<x-slot:footer>
					<a href="{{ route('application.recommendation.create', $application) }}"
						class="btn btn-sm btn-danger">
						<x-icon.font-awesome class="fa-pencil-square" />
						Change Recommendation
					</a>
				</x-slot:footer>
			@endcan
		@else
			@if ($application->assessment)
				<x-alert>
					<x-slot:icon>
						<x-icon.font-awesome class="fa-box-open fs-2" />
					</x-slot:icon>
					No recommendation found
					@can('create', [\App\Models\Recommendation::class, $application])
						<x-slot:options>
							<a href="{{ route('application.recommendation.create', $application) }}"
								class="btn btn-sm btn-primary">
								<x-icon.font-awesome class="fa-pen-to-square" />
								Make a Recommendation
							</a>
						</x-slot:options>
					@endcan
				</x-alert>
			@elseif (!$application->finishedInterview())
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
			@else
				<x-alert>
					<x-slot:icon>
						<x-icon.font-awesome class="fa-exclamation-circle fs-2" />
					</x-slot:icon>
					Make an assessment first before making a recommendation
					@can('create', [\App\Models\Assessment::class, $application])
						<x-slot:options>
							<a href="{{ route('application.assessment.create', $application) }}"
								class="btn btn-sm btn-primary">
								<x-icon.font-awesome class="fa-pen-to-square" />
								Make an Assessment
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
			You do not have permission to view the recommendation
		</x-alert>
	@endcan
</x-card>
