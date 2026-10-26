<x-card>
	<x-slot:header>Assessment</x-slot:header>
	@can('view', [\App\Models\Assessment::class])
        <div class="mb-5" id="interview">
            @if ($inlineApplicationDetails ?? false)
                @include('interview.partials.latest', ['canComplete' => $canCompleteInterview])
            @else
                <livewire:interview.index :client="$application->client" :assignment-uuid="$application->current_claimant_assignment_uuid" />
            @endif
            @if (! $application->finishedInterview() && ! $application->current_recommendation_uuid)
                @can('create', [\App\Models\Interview::class, $application->client])
                    <livewire:interview.create :client="$application->client" :key="'interview-create-'.$application->current_claimant_assignment_uuid" />
                @endcan
            @endif
        </div>
        <div class="separator separator-dashed my-5"></div>
		@if ($application->assessment)
            @if ($inlineApplicationDetails ?? false)
                @include('livewire.assessment.show', ['assessment' => $application->assessment])
            @else
                <livewire:assessment.show :assessment="$application->assessment" defer />
            @endif
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
					Complete the current claimant's interview before making an assessment.
					@can('create', [\App\Models\Interview::class, $application->client])
						<x-slot:options>
							<a href="#interview"
								class="btn btn-sm btn-primary">
								<x-icon.font-awesome class="fa-up-right-from-square" />
								Schedule Interview
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
