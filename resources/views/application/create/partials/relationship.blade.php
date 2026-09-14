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
			Select a {{ $clientUuid ? '' : 'Client Draft' }} {{ $clientUuid && $beneficiaryUuid ? '' : 'and a' }}
			{{ $beneficiaryUuid ? '' : 'Beneficiary Draft' }} to continue.
		</x-alert>
	@endif
</x-card>
