<div class='row'>
	@role('staff')
		<div class="col-12 col-md-6">
			<x-card>
				<x-slot:header>Clients Per Month</x-slot:header>
				<livewire:client.charts.per-month />
			</x-card>
		</div>
	@endrole
</div>
