<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		<x-data-table :columns="$columns" :data="$mappedFuneralAssistanceTypes" :data-table-id="$dataTableId"
            :card-fields="['primary' => 'name', 'secondary' => 'description', 'tertiary' => 'workflow']" />
	</x-card>
	<x-card>
		<x-slot:header>Create New Type of Funeral Assistance Type</x-slot:header>
		<livewire:funeral-assistance-type.create defer />
	</x-card>
</div>
