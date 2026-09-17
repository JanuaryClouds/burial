<div class="d-flex flex-column gap-4 border border-2 border-dashed border-gray-200 rounded py-3 px-4">
	<div class="row">
		<div class="col-12 col-md-8 col-lg-5">
			<x-form.input wire:model="name"
				label="Full name"
				name="name"
				:required="true" />
		</div>
		<div class="col-12 col-md-4 col-lg-3">
			<x-form.input wire:model="age"
				label="Age"
				name="age"
				:required="true"
				type="number" />
		</div>
		<div class="col-6 col-md-3 col-lg-2">
			<x-form.select wire:model="civilId"
				name="civilId"
				label="Civil Status"
				:required="true"
				:selected="$civilId"
				:options="$civilStatus ?? []" />
		</div>
		<div class="col-6 col-md-3 col-lg-2">
			<x-form.select wire:model="sexId"
				name="sexId"
				label="Sex"
				:required="true"
				:selected="$sexId"
				:options="$genders ?? []" />
		</div>
		<div class="col-12 col-md-6 col-xl-3">
			<x-form.select wire:model='relationshipId'
				name="relationshipId"
				label="Relationship to the Beneficiary"
				:selected="$relationshipId"
				:options="$relationships ?? []"
				:required="true" />
		</div>
		<div class="col-12 col-md-6 col-xl-4">
			<x-form.input wire:model="occupation"
				name="occupation"
				label="Occupation" />
		</div>
		<div class="col-12 col-md-4 col-xl-2">
			<x-form.input wire:model="income"
				name="income"
				label="Monthly Income" />
		</div>
	</div>
	<div class="d-flex justify-content-end align-items-center gap-2">
		<x-button wire:click='save'
			wire:loading.attr='disabled'
			class="btn-sm btn-success">
			<x-icon.font-awesome class="fa-floppy-disk" />
			Save Changes
		</x-button>
		<x-button wire:click="removeFamilyMember"
			wire:loading.attr="disabled"
			class="btn-sm btn-warning">
			<x-icon.font-awesome class="fa-trash-can" />
			Remove Family Member
		</x-button>
	</div>
</div>
