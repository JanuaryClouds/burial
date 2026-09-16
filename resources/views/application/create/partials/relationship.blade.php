<x-card>
	<x-slot:header>Relationship to the Beneficiary</x-slot:header>
	@if ($beneficiaryUuid && $clientUuid)
		<x-form.select wire:model.live="relationshipId"
			name="relationshipId"
			label="Relationship to the Beneficiary"
			:required="true"
			:options="$relationships ?? []" />
	@else
		<x-alert>
			<x-slot:icon>
				<x-icon.font-awesome class="fa-exclamation-circle fs-2" />
			</x-slot:icon>
			Please provide the following to continue:
			{{ !$clientUuid ? 'Client Draft, ' : '' }}
			{{ !$beneficiaryUuid ? 'Beneficiary Draft' : '' }}
		</x-alert>
	@endif
</x-card>
