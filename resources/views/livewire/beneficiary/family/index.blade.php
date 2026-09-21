<div class="d-flex flex-column gap-4 align-items-center">
	@foreach ($family as $member)
		<livewire:beneficiary.family.show :member="$member"
			wire:key="family-member-{{ $member->uuid }}" />
	@endforeach
	@if (!$beneficiary->application)
		@if ($family->count() < 5)
			@can('create', [\App\Models\BeneficiaryFamily::class, $beneficiary])
				<div class="separator separator-dashed my-4 w-100"></div>
				<div class="d-flex justify-content-start">
					<h5>Add a Family Member</h5>
				</div>
				<div class="d-flex flex-column gap-4 border border-2 border-dashed border-gray-200 rounded py-3 px-4">
					<div class="row">
						<div class="col-12 col-md-8 col-lg-5">
							<x-form.input wire:model="form.name"
								label="Full name"
								name="form.name"
								:required="true" />
						</div>
						<div class="col-12 col-md-4 col-lg-3">
							<x-form.input wire:model="form.age"
								label="Age"
								name="form.age"
								:required="true"
								type="number" />
						</div>
						<div class="col-6 col-md-3 col-lg-2">
							<x-form.select wire:model="form.civilId"
								name="form.civilId"
								label="Civil Status"
								:required="true"
								:selected="$form->civilId ?? ''"
								:options="$civilStatus ?? []" />
						</div>
						<div class="col-6 col-md-3 col-lg-2">
							<x-form.select wire:model="form.sexId"
								name="form.sexId"
								label="Sex"
								:required="true"
								:selected="$form->sexId ?? ''"
								:options="$genders ?? []" />
						</div>
						<div class="col-12 col-md-6 col-xl-3">
							<x-form.select wire:model="form.relationshipId"
								name="form.relationshipId"
								label="Relationship to the Beneficiary"
								:selected="$form->relationshipId ?? ''"
								:options="$relationships ?? []"
								:required="true" />
						</div>
						<div class="col-12 col-md-6 col-xl-4">
							<x-form.input wire:model="form.occupation"
								name="form.occupation"
								label="Occupation" />
						</div>
						<div class="col-12 col-md-4 col-xl-2">
							<x-form.input wire:model="form.income"
								name="form.income"
								label="Monthly Income" />
						</div>
					</div>
					<div class="d-flex justify-content-end align-items-center gap-2">
						<x-button wire:click='save'
							class="btn-sm btn-success"
							wire:loading.attr='disabled'
							wire:dirty>
							<x-icon.font-awesome class="fa-floppy-disk" />
							Save
						</x-button>
					</div>
				</div>
			@endcan
		@else
			<p class="text-muted">You can only add 5 family members</p>
		@endif
	@endif
</div>
