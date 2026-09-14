<x-card>
	<x-slot:header>Selected Client</x-slot:header>
	@if ($clientUuid)
		<livewire:client.show :uuid="$clientUuid"
			defer />
		<x-slot:footer>
			<x-button wire:click="$set('clientUuid', '')"
				wire:loading.attr='disabled'
				class="btn-sm btn-danger">
				<x-icon.font-awesome class="fa-xmark" />
				Unselect Client Draft
			</x-button>
		</x-slot:footer>
	@else
		<x-form.select wire:model.live="clientUuid"
			name="clientUuid"
			label="Selected Client Draft"
			:required="true"
			:selected="session('client_uuid') ?? null"
			:options="$clientOptions ?? []" />
		<x-alert>
			<x-slot:icon>
				<x-icon.font-awesome class="fa-box-open fs-2" />
			</x-slot:icon>
			No Client Selected
			<x-slot:options>
				<a href="{{ route('client.create') }}"
					class="btn btn-sm btn-primary"
					role="button">
					<x-icon.font-awesome class="fa-up-right-from-square" />
					Draft a New Client
				</a>
			</x-slot:options>
		</x-alert>
	@endif
</x-card>
