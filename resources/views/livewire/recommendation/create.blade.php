<x-slot:page_title>{{ $application->tracking_no }} | Create Recommendation</x-slot:page_title>
<x-slot:page_subtitle>Funeral Assistance System | CSWDO Taguig</x-slot:page_subtitle>

<div class="d-flex flex-column gap-6">
	<div class="row">
		<div class="col-12 col-lg-8">
			{{-- Application Summary to help the social worker write the recommendation --}}
			<x-card>
				<x-slot:header>Application Summary</x-slot:header>
				<livewire:application.summary :application="$application"
					defer />
				<x-slot:footer>
					<a href="{{ route('application.show', $application) }}"
						class="btn btn-sm btn-light">
						<x-icon.font-awesome class="fa-up-right-from-square" />
						Show Application
					</a>
					<a href="{{ route('client.show', $application->client) }}"
						class="btn btn-sm btn-light">
						<x-icon.font-awesome class="fa-up-right-from-square" />
						Show Client
					</a>
					<a href="{{ route('beneficiary.show', $application->beneficiary) }}"
						class="btn btn-sm btn-light">
						<x-icon.font-awesome class="fa-up-right-from-square" />
						Show Beneficiary
					</a>
				</x-slot:footer>
			</x-card>
		</div>
		<div class="col-12 col-lg-4">
			{{-- Assessment to help the social worker write the recommendation --}}
			<x-card>
				<x-slot:header>Assessment</x-slot:header>
				<livewire:assessment.show :assessment="$application->assessment"
					defer />
			</x-card>
		</div>
	</div>
	<div class="row">
		<div class="col-12 col-lg-6">
			{{-- Previous Recommendation --}}
			<x-card>
				<x-slot:header>Previous Recommendation</x-slot:header>
				@if ($application->currentRecommendation())
					<livewire:recommendation.show :recommendation="$application->currentRecommendation()"
						defer />
				@else
					<div class="d-flex flex-center flex-column h-200px gap-5">
						<x-icon.font-awesome class="fa-box-open fs-2" />
						<p class="fs-6 text-muted">No Recommendation</p>
					</div>
				@endif
			</x-card>
		</div>
		<div class="col-12 col-lg-6">
			<x-card>
				<x-slot:header>Create Recommendation</x-slot:header>
				@if (!$createNew)
					<x-alert>
						<x-slot:icon>
							<x-icon.font-awesome class="fa-circle-plus fs-2" />
						</x-slot:icon>
						@if ($application->recommendations->count() > 0)
							Need to change the recommendation?
						@else
							Create a recommendation
						@endif
						<x-slot:options>
							<x-button wire:click="$set('createNew', true)"
								class="btn btn-sm btn-primary">
								<x-icon.font-awesome class="fa-plus" />
								Create New Recommendation
							</x-button>
						</x-slot:options>
					</x-alert>
				@endif
				@if ($createNew)
					<x-form.select name="funeralAssistanceTypeUuid"
						wire:model.live='funeralAssistanceTypeUuid'
						:options="$funeralAssistanceTypes"
						label="Funeral Assistance Type"
						:required="true" />
					@if ($funeralAssistanceTypeUuid)
						<x-form.input name="amountExtended"
							wire:model.live.blur='amountExtended'
							label="Amount to Extend"
							required
							type="number" />
					@endif
					@if ($amountExtended)
						<x-form.select name="modeOfAssistanceId"
							wire:model.live='modeOfAssistanceId'
							:options="$modeOfAssistances"
							label="Mode of Assistance"
							:required="true" />
					@endif
					@if ($funeralAssistanceTypeUuid && $modeOfAssistanceId && $amountExtended)
						<x-slot:footer>
							@if ($createNew)
								<x-button wire:click="$set('createNew', false)"
									class="btn-sm btn-light">
									<x-icon.font-awesome class="fa-xmark" />
									Cancel
								</x-button>
							@endif
							<div class="d-flex justify-content-end gap-2">
								<x-button wire:click='save'
									wire:loading.attr='disabled'
									class="btn-sm btn-success">
									<i class="fa-solid fa-floppy-disk"></i>
									<span wire:loading.remove>Save</span>
									<span wire:loading>Saving...</span>
								</x-button>
							</div>
						</x-slot:footer>
					@endif
				@endif
			</x-card>
		</div>
	</div>
</div>
