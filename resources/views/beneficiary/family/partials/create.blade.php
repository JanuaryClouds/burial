<x-card>
	<x-slot:header>Family Composition</x-slot:header>
	<div class="d-flex flex-column gap-4">
		<div class="row">
			<div class="col-12 col-md-6">
				<x-callout class="bg-danger-subtle border-danger-subtle h-100">
					<x-slot:icon>
						<x-icon.font-awesome class="fa-triangle-exclamation text-danger fs-2" />
					</x-slot:icon>
					<x-slot:title>
						<p class="text-danger fs-6 fw-bold">This from has required fields</p>
					</x-slot:title>
					<p class="text-danger">All fields marked with an asterisk (*) are required to be filled out. Leave blank if not
						required and inapplicable.</p>
				</x-callout>
			</div>
			<div class="col-12 col-md-6 d-flex flex-grow">
				<x-callout class="bg-info-subtle border-info-subtle h-100">
					<x-slot:icon>
						<x-icon.font-awesome class="fa-info-circle text-info fs-2" />
					</x-slot:icon>
					<p class="text-info">You can only add up to five family members.</p>
				</x-callout>
			</div>
		</div>

		@forelse ($family as $index => $member)
			<div wire:key="family-member-{{ $index }}"
				class="d-flex flex-column gap-4 border border-2 border-dashed border-gray-200 rounded py-3 px-4">
				<div class="row">
					<div class="col-12 col-lg-5">
						<x-form.input wire:model="family.{{ $index }}.name"
							label="Full name"
							name="family.{{ $index }}.name"
							:required="true" />
					</div>
					<div class="col-4 col-lg-3">
						<x-form.input wire:model="family.{{ $index }}.dateOfBirth"
							label="Date of Birth"
							name="family.{{ $index }}.dateOfBirth"
							:required="true"
							type="date" />
					</div>
					<div class="col-4 col-lg-2">
						<x-form.select wire:model="family.{{ $index }}.civilId"
							name="family.{{ $index }}.civilId"
							label="Civil Status"
							:required="true"
							:selected="$family[$index]['civilId'] ?? ''"
							:options="$civilStatus ?? []" />
					</div>
					<div class="col-4 col-lg-2">
						<x-form.select wire:model="family.{{ $index }}.sexId"
							name="family.{{ $index }}.sexId"
							label="Sex"
							:required="true"
							:selected="$family[$index]['sexId'] ?? ''"
							:options="$genders ?? []" />
					</div>
				</div>
				<div class="row">
					<div class="col-12 col-md-4">
						<x-form.select wire:model='family.{{ $index }}.relationshipId'
							name="family.{{ $index }}.relationshipId"
							label="Relationship to the Beneficiary"
							:selected="$family[$index]['relationshipId'] ?? ''"
							:options="$relationships ?? []"
							:required="true" />
					</div>
					<div class="col-6 col-md-4">
						<x-form.input wire:model="family.{{ $index }}.occupation"
							name="family.{{ $index }}.occupation"
							label="Occupation" />
					</div>
					<div class="col-6 col-md-4">
						<x-form.input wire:model="family.{{ $index }}.income"
							name="family.{{ $index }}.income"
							label="Monthly Income" />
					</div>
				</div>
				<div class="d-flex justify-content-end align-items-center">
					<x-button wire:click="removeFamilyMember({{ $index }})"
						wire:loading.attr="disabled"
						class="btn-sm btn-warning">
						<x-icon.font-awesome class="fa-trash-can" />
						Remove Family Member
					</x-button>
				</div>
			</div>
		@empty
			<x-alert>
				<x-slot:icon>
					<x-icon.font-awesome class="fa-box-open fs-2" />
				</x-slot:icon>
				No family members yet
				<x-slot:options>
					<x-button wire:click="addFamilyMember"
						wire:loading.attr="disabled"
						class="btn-sm btn-primary">
						<x-icon.font-awesome class="fa-plus" />
						Add Family Member
					</x-button>
				</x-slot:options>
			</x-alert>
		@endforelse
		@if (count($family) > 0 && count($family) < 5)
			<div class="d-flex justify-content-center align-items-center">
				<x-button wire:click="addFamilyMember"
					wire:loading.attr="disabled"
					class="btn-sm btn-primary">
					<x-icon.font-awesome class="fa-plus" />
					Add Family Member
				</x-button>
			</div>
		@endif
	</div>
</x-card>
