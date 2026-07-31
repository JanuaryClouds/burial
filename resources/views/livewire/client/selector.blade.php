<x-card>
	<x-slot:header>Client Information</x-slot:header>
	@if (isset($clients) && count($clients) > 0)
		<div class="row">
			<div class="col-12 col-md-6">
				<x-form.select name="client_uuid"
					id="client_uuid"
					label="Select Client Record"
					wire:model.live="selectedClientUuid"
					:options="$clients ?? []" />
			</div>
			<div class="col-12 col-md-6">
				<x-form.select name="relationship_id"
					label="Relationship to Beneficiary"
					:options="$relationships ?? []" />
			</div>
		</div>
		@if ($selectedClient)
			@include('client.partials.create.form', [
				'client' => $selectedClient,
			])
		@endif
	@endif
	<x-slot:footer>
		<a name=""
			id=""
			class="btn btn-primary"
			href="{{ route('client.create') }}"
			role="button">
			<i class="fa-solid fa-plus"></i>
			Create New Client Record
		</a>
	</x-slot:footer>
</x-card>
