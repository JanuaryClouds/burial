<div class="d-flex flex-column gap-6"
	wire:poll.10s='loadData'>
	<x-card>
		<x-data-table :columns="$columns" :data="$mappedUsers" :data-table-id="$dataTableId"
            :card-fields="['primary' => 'name', 'secondary' => 'email', 'tertiary' => 'contact_number', 'badge' => 'role']" />
	</x-card>
	<x-card>
		<x-slot:header>Staff invitations</x-slot:header>
		<a class="btn btn-primary" href="{{ route('user.invitations.index') }}">Invite staff / Manage invitations</a>
	</x-card>
</div>
