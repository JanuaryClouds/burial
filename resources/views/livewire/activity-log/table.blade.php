<div wire:poll.10s="loadData">
    <x-card>
        <x-data-table :columns="$columns" :data="$mappedLogs" :data-table-id="$this->tableId()"
            :card-fields="['primary' => 'description', 'secondary' => 'source', 'tertiary' => $kind === 'security' ? 'risk_score' : 'source', 'date' => 'logged_at']" />
    </x-card>
</div>
