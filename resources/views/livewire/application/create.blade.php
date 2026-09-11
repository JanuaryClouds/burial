<x-slot:page_title>Create Application</x-slot:page_title>
<x-slot:page_subtitle>Funeral Assistance System | CSWDO Taguig</x-slot:page_subtitle>
<div class="d-flex flex-column gap-6">
	<div class="row">
		<div class="col-12 col-lg-4 d-flex flex-column gap-4">
			<x-card>
				<x-slot:header>Selected Client</x-slot:header>
				<x-form.select wire:model.live.blur="client_uuid"
					name="client_uuid"
					label="Draft Client Records"
					:required="true"
					:selected="session('client_uuid') ?? null"
					:options="$clientOptions ?? []" />
			</x-card>
			<x-card>
				<x-slot:header>Selected Beneficiary</x-slot:header>
				<x-form.select wire:model.live.blur="beneficiary_uuid"
					name="beneficiaryUuid"
					label="Draft Beneficiary Records"
					:required="true"
					:selected="session('beneficiary_uuid') ?? null"
					:options="$beneficiaryOptions ?? []" />
			</x-card>
			<x-card>
				<x-form.select wire:model.live.blur="relationshipId"
					name="relationshipId"
					label="Relationship to the Beneficiary"
					:required="true"
					:options="$relationships ?? []" />
			</x-card>
		</div>
		<div class="col-12 col-lg-8 d-flex flex-column gap-4">

		</div>
	</div>
	<x-card>
		<x-slot:header>Submit Documents</x-slot:header>
		<div class="d-flex flex-column gap-4">
			<x-callout class="bg-info-subtle border-info">
				<x-slot:icon>
					<x-icon.font-awesome class="fa-exclamation-circle text-info" />
				</x-slot:icon>
				<div class="text-info">
					During the interview, please bring hard copies of the documents you have submitted as soft copies.
				</div>
			</x-callout>
			@include('application.create.partials.documents')
		</div>
	</x-card>
</div>
