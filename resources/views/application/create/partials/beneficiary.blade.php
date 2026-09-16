<x-card>
	<x-slot:header>Selected Beneficiary</x-slot:header>
	@if ($beneficiaryUuid)
		<livewire:beneficiary.show :uuid="$beneficiaryUuid"
			defer />
		<x-slot:footer>
			<x-button wire:click="$set('beneficiaryUuid', '')"
				wire:loading.attr='disabled'
				class="btn-sm btn-danger">
				<x-icon.font-awesome class="fa-xmark" />
				Unselect Beneficiary Draft
			</x-button>
		</x-slot:footer>
	@else
		<x-form.select wire:model.live="beneficiaryUuid"
			name="beneficiaryUuid"
			label="Draft Beneficiary Draft"
			:required="true"
			:selected="session('beneficiary_uuid') ?? null"
			:options="$beneficiaryOptions ?? []" />
		<x-alert>
			<x-slot:icon>
				<x-icon.font-awesome class="fa-box-open fs-2" />
			</x-slot:icon>
			No Beneficiary Selected
			<x-slot:options>
				<a href="{{ route('beneficiary.create') }}"
					class="btn btn-sm btn-primary"
					role="button">
					<x-icon.font-awesome class="fa-up-right-from-square" />
					Draft a New Beneficiary
				</a>
			</x-slot:options>
		</x-alert>
	@endif
</x-card>
