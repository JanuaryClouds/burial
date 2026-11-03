<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		<x-data-table :columns="$columns" :data="$mappedUsers" :data-table-id="$dataTableId"
            :card-fields="['primary' => 'name', 'secondary' => 'email', 'tertiary' => 'contact_number', 'badge' => 'role']" />
	</x-card>
	<x-card>
		<x-slot:header>Create New Staff</x-slot:header>
		<livewire:user.create defer />
	</x-card>
</div>
