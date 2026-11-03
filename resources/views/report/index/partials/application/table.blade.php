<div>
	<div class="row">
		<div class="col-12">
			<x-card>
				<x-slot:header>Applications</x-slot:header>
				<x-data-table :columns="$columns" :data="$applications" data-table-id="applications-table"
                    :card-fields="['primary' => 'tracking_number', 'secondary' => 'client', 'tertiary' => 'beneficiary', 'badge' => 'status']" />
			</x-card>
		</div>
	</div>
</div>
