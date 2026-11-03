<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		<x-data-table :columns="$columns" :data="$mappedLogs" :data-table-id="$dataTableId"
            :card-fields="['primary' => 'description', 'secondary' => 'source', 'date' => 'logged_at']" />
	</x-card>
</div>
