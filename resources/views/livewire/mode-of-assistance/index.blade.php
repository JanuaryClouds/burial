<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		<x-data-table :columns="$columns" :data="$mappedModeOfAssistances" :data-table-id="$dataTableId"
            :card-fields="['primary' => 'name']" />
	</x-card>
	<x-card>
		<x-slot:header>Create New Mode of Assistance</x-slot:header>
		<livewire:mode-of-assistance.create defer />
	</x-card>
</div>
